@php
    $stats = $this->getTotalStats();
    $byCategory = $this->getByCategory();
    $budgets = $this->getBudgetData();
    
    // Map month number to German month name
    $months = [
        1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
        5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'
    ];
@endphp

<div class="space-y-6">
        <!-- Header with Title -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Budget-Übersicht</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    @if ($selectedPeriod === 'monthly')
                        {{ $months[$selectedMonth] }} {{ $selectedYear }}
                    @elseif ($selectedPeriod === 'quarterly')
                        Q{{ ceil($selectedMonth / 3) }} {{ $selectedYear }}
                    @else
                        Jahr {{ $selectedYear }}
                    @endif
                </p>
            </div>
            <div class="text-5xl opacity-20">
                <x-heroicon-o-chart-bar class="w-20 h-20 text-blue-500" />
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-100 p-6 dark:from-blue-900 dark:to-indigo-900 dark:border-blue-800">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <x-heroicon-o-funnel class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" />
                Filter
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zeitraum</label>
                    <select wire:model.live="selectedPeriod" class="w-full px-4 py-2.5 border border-blue-200 rounded-lg bg-white dark:bg-gray-800 dark:border-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="monthly">📅 Monatlich</option>
                        <option value="quarterly">📊 Quartalsweise</option>
                        <option value="yearly">📈 Jährlich</option>
                    </select>
                </div>
                
                @if ($selectedPeriod !== 'yearly')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monat</label>
                        <select wire:model.live="selectedMonth" class="w-full px-4 py-2.5 border border-blue-200 rounded-lg bg-white dark:bg-gray-800 dark:border-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="1">🎄 Januar</option>
                            <option value="2">❄️ Februar</option>
                            <option value="3">🌸 März</option>
                            <option value="4">🌷 April</option>
                            <option value="5">🌻 Mai</option>
                            <option value="6">☀️ Juni</option>
                            <option value="7">🌞 Juli</option>
                            <option value="8">🏖️ August</option>
                            <option value="9">🍂 September</option>
                            <option value="10">🎃 Oktober</option>
                            <option value="11">🍁 November</option>
                            <option value="12">🎅 Dezember</option>
                        </select>
                    </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jahr</label>
                    <select wire:model.live="selectedYear" class="w-full px-4 py-2.5 border border-blue-200 rounded-lg bg-white dark:bg-gray-800 dark:border-blue-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @for ($i = 2020; $i <= now()->year + 1; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Overall Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Gesamtbudget</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_budget'], 0, ',', '.') }}<span class="text-lg ml-1">€</span></p>
                    </div>
                    <div class="opacity-20">
                        <x-heroicon-o-banknotes class="w-12 h-12" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Ausgegeben</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_spent'], 0, ',', '.') }}<span class="text-lg ml-1">€</span></p>
                    </div>
                    <div class="opacity-20">
                        <x-heroicon-o-arrow-trending-down class="w-12 h-12" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Verbleibend</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_remaining'], 0, ',', '.') }}<span class="text-lg ml-1">€</span></p>
                    </div>
                    <div class="opacity-20">
                        <x-heroicon-o-arrow-trending-up class="w-12 h-12" />
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br {{ $stats['overall_percentage'] > 100 ? 'from-red-500 to-red-600' : ($stats['overall_percentage'] > 80 ? 'from-yellow-500 to-yellow-600' : 'from-purple-500 to-purple-600') }} rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="opacity-90 text-sm font-medium">Auslastung</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['overall_percentage'], 1, ',', '.') }}<span class="text-lg ml-1">%</span></p>
                    </div>
                    <div class="opacity-20">
                        <x-heroicon-o-chart-bar class="w-12 h-12" />
                    </div>
                </div>
            </div>
        </div>

        <!-- By Category -->
        @if ($byCategory->count() > 0)
            <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-900 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" />
                        Ausgaben nach Hauptkategorie
                    </h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($byCategory as $cat)
                        <div class="px-6 py-5 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $cat['category'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $cat['count'] }} {{ $cat['count'] === 1 ? 'Budget' : 'Budgets' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900 dark:text-white">{{ number_format($cat['spent'], 2, ',', '.') }} € <span class="font-normal text-gray-500">/</span> {{ number_format($cat['budget'], 2, ',', '.') }} €</p>
                                    <p class="text-sm font-semibold mt-1 {{ $cat['percentage'] > 100 ? 'text-red-600 dark:text-red-400' : ($cat['percentage'] > 80 ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}">
                                        {{ number_format($cat['percentage'], 1, ',', '.') }}%
                                    </p>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700 overflow-hidden">
                                <div
                                    class="h-3 rounded-full transition-all {{ $cat['percentage'] > 100 ? 'bg-gradient-to-r from-red-500 to-red-600' : ($cat['percentage'] > 80 ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-green-500 to-emerald-600') }}"
                                    style="width: {{ min($cat['percentage'], 100) }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Detailed Budget List -->
        @if ($budgets->count() > 0)
            <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-900 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <x-heroicon-o-list-bullet class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" />
                        Alle Budgets
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Kategorie</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Unterkategorie</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">Budget</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">Ausgegeben</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">Verbleibend</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">Auslastung</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($budgets as $budget)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $budget['category'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $budget['subcategory'] }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($budget['budget'], 2, ',', '.') }} €</td>
                                    <td class="px-6 py-4 text-sm text-right {{ $budget['spent'] > $budget['budget'] ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-gray-100' }}">
                                        {{ number_format($budget['spent'], 2, ',', '.') }} €
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right {{ $budget['remaining'] < 0 ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-green-600 dark:text-green-400' }}">
                                        {{ number_format($budget['remaining'], 2, ',', '.') }} €
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ match($budget['status']) {
                                            'over' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                                            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                                            default => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                        } }}">
                                            {{ number_format($budget['percentage'], 1, ',', '.') }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-md p-12 text-center dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                <x-heroicon-o-inbox class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" />
                <p class="mt-4 text-gray-600 dark:text-gray-400 text-lg font-medium">Keine Budgets für den ausgewählten Zeitraum vorhanden.</p>
                <a href="/admin/budgets" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition">
                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                    Budget erstellen
                </a>
            </div>
        @endif
    </div>