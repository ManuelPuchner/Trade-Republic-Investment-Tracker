<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Pages\ViewCategory;
use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Kategorien';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Finanz Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Grundinformationen')
                    ->description('Basis-Informationen für die Kategorie')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-tag')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-hashtag'),
                        
                        ColorPicker::make('color')
                            ->label('Farbe')
                            ->nullable(),
                        
                        TextInput::make('icon')
                            ->label('Icon')
                            ->placeholder('heroicon-o-tag')
                            ->helperText('Heroicon name, z.B. heroicon-o-shopping-cart')
                            ->nullable()
                            ->prefixIcon('heroicon-o-sparkles'),
                        
                        Textarea::make('description')
                            ->label('Beschreibung')
                            ->rows(3)
                            ->columnSpanFull()
                            ->nullable(),
                    ]),

                Section::make('Budget-Zuordnung')
                    ->description('Kategorisierung für Budget-Tracking')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('category')
                            ->label('Hauptkategorie')
                            ->placeholder('z.B. Wohnen, Transport, Lebensmittel')
                            ->helperText('Wird für die Gruppierung in der Budget-Übersicht verwendet')
                            ->maxLength(255)
                            ->nullable()
                            ->prefixIcon('heroicon-o-folder'),
                        
                        TextInput::make('subcategory')
                            ->label('Unterkategorie')
                            ->placeholder('z.B. Miete, Benzin, Einkaufen')
                            ->helperText('Detaillierte Unterkategorisierung')
                            ->maxLength(255)
                            ->nullable()
                            ->prefixIcon('heroicon-o-folder-open'),
                        
                        Toggle::make('is_income_category')
                            ->label('Einnahmen-Kategorie')
                            ->helperText('Aktivieren, wenn dies eine Kategorie für Einnahmen ist (z.B. Gehalt, Bonus)')
                            ->default(false)
                            ->inline(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon(fn ($record) => $record->icon ?? 'heroicon-o-tag'),
                
                TextColumn::make('category')
                    ->label('Hauptkategorie')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('–'),
                
                TextColumn::make('subcategory')
                    ->label('Unterkategorie')
                    ->searchable()
                    ->sortable()
                    ->placeholder('–')
                    ->toggleable(),
                
                IconColumn::make('is_income_category')
                    ->label('Einnahme')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-trending-up')
                    ->falseIcon('heroicon-o-arrow-trending-down')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                
                ColorColumn::make('color')
                    ->label('Farbe')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('transactions_count')
                    ->label('Transaktionen')
                    ->counts('transactions')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info'),
                
                TextColumn::make('budgets_count')
                    ->label('Budgets')
                    ->counts('budgets')
                    ->sortable()
                    ->icon('heroicon-o-banknotes')
                    ->color('warning'),
                
                TextColumn::make('description')
                    ->label('Beschreibung')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_income_category')
                    ->label('Kategorie-Typ')
                    ->placeholder('Alle Kategorien')
                    ->trueLabel('Nur Einnahmen')
                    ->falseLabel('Nur Ausgaben')
                    ->native(false),
                
                SelectFilter::make('category')
                    ->label('Hauptkategorie')
                    ->options(fn () => Category::whereNotNull('category')
                        ->distinct()
                        ->pluck('category', 'category')
                        ->toArray())
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'view' => ViewCategory::route('/{record}'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}