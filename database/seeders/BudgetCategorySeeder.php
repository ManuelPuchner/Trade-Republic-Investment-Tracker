<?php

namespace Database\Seeders;

use App\Models\BudgetCategory;
use Illuminate\Database\Seeder;

class BudgetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Einnahmen - Arbeitseinkommen
            ['name' => 'Bruttogehalt', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Bruttolohn', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Nettogehalt', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Nettolohn', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Überstundenvergütung', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Prämien und Boni', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => '13. Gehalt', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => '14. Gehalt', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Trinkgelder', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Provisionen', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Nebenerwerbseinkommen', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Selbstständige Einkünfte', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],
            ['name' => 'Freiberufliche Honorare', 'category' => 'Einnahmen', 'subcategory' => 'Arbeitseinkommen'],

            // Einnahmen - Staatliche Leistungen
            ['name' => 'Familienbeihilfe', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Kinderbetreuungsgeld', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Arbeitslosengeld', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Notstandshilfe', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Pensionszahlungen', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Mindestsicherung', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Sozialhilfe', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Studienbeihilfe', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Wohnbeihilfe', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Pendlerpauschale', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],
            ['name' => 'Pflegegeld', 'category' => 'Einnahmen', 'subcategory' => 'Staatliche Leistungen & Beihilfen'],

            // Einnahmen - Kapitalerträge
            ['name' => 'Zinsen aus Sparkonten', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],
            ['name' => 'Dividenden aus Aktien', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],
            ['name' => 'Mieteinnahmen', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],
            ['name' => 'Pachteinnahmen', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],
            ['name' => 'Kapitalerträge aus Investmentfonds', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],
            ['name' => 'Kryptowährungsgewinne', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],
            ['name' => 'Lizenzgebühren', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],
            ['name' => 'Tantiemen', 'category' => 'Einnahmen', 'subcategory' => 'Kapitalerträge & Vermögen'],

            // Einnahmen - Sonstige
            ['name' => 'Unterhaltszahlungen empfangen', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Kindergeld von Ex-Partner', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Erbschaften', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Schenkungen', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Steuerrückerstattungen', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Versicherungsauszahlungen', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Verkauf von Gegenständen', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Nebenverdienste', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Cashback', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],
            ['name' => 'Bonusprogramme', 'category' => 'Einnahmen', 'subcategory' => 'Sonstige Einnahmen'],

            // Ausgaben - Wohnen Grundkosten
            ['name' => 'Miete', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Kaltmiete', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Hypothek', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Wohnkredit', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Betriebskosten', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Warmmiete', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Hausverwaltungskosten', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Grundsteuer', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],
            ['name' => 'Instandhaltungsrücklage', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Grundkosten'],

            // Ausgaben - Wohnen Energie
            ['name' => 'Strom', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Gas', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Fernwärme', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Heizöl', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Wasser', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Abwasser', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Müllabfuhr', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Kanalgebühr', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Rauchfangkehrer', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],
            ['name' => 'Winterdienst', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Energie & Versorgung'],

            // Ausgaben - Wohnen Kommunikation
            ['name' => 'Internet', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Kommunikation & Medien'],
            ['name' => 'Festnetztelefon', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Kommunikation & Medien'],
            ['name' => 'Kabelfernsehen', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Kommunikation & Medien'],
            ['name' => 'GIS-Gebühr', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Kommunikation & Medien'],
            ['name' => 'Satelliten-TV', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Kommunikation & Medien'],

            // Ausgaben - Wohnen Haushalt
            ['name' => 'Möbel', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Haushaltsgeräte', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Reparaturen Haushalt', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Wartung Haushalt', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Dekoration', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Bettwäsche', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Handtücher', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Reinigungsmittel', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Haushaltsutensilien', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Werkzeug', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Gartenmöbel', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Gartengeräte', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],
            ['name' => 'Pflanzen', 'category' => 'Ausgaben', 'subcategory' => 'Wohnen - Haushalt & Einrichtung'],

            // Ausgaben - Mobilität Auto
            ['name' => 'Autokredit', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Leasing', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'KFZ-Versicherung Haftpflicht', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'KFZ-Versicherung Kasko', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Motorfahrzeugsteuer', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'NoVA', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Treibstoff Benzin', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Treibstoff Diesel', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Autobahnvignette', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Parkgebühren', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Parkpickerl', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Garagenmiete', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Stellplatz', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Autowäsche', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Service Auto', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Wartung Auto', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Reparaturen Auto', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Reifenwechsel', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => '§57a-Begutachtung', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'ÖAMTC Mitgliedschaft', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'ARBÖ Mitgliedschaft', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],
            ['name' => 'Maut', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Auto'],

            // Ausgaben - Mobilität ÖPNV
            ['name' => 'Jahreskarte', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Öffentliche Verkehrsmittel'],
            ['name' => 'Monatskarte', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Öffentliche Verkehrsmittel'],
            ['name' => 'Einzelfahrscheine', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Öffentliche Verkehrsmittel'],
            ['name' => 'Zugtickets ÖBB', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Öffentliche Verkehrsmittel'],
            ['name' => 'Klimaticket', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Öffentliche Verkehrsmittel'],

            // Ausgaben - Mobilität Sonstige
            ['name' => 'Fahrrad Anschaffung', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],
            ['name' => 'Fahrrad Reparatur', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],
            ['name' => 'E-Scooter', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],
            ['name' => 'E-Bike', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],
            ['name' => 'Taxi', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],
            ['name' => 'Uber', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],
            ['name' => 'Carsharing', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],
            ['name' => 'Flugtickets', 'category' => 'Ausgaben', 'subcategory' => 'Mobilität - Sonstige Mobilität'],

            // Ausgaben - Versicherungen Personen
            ['name' => 'Krankenversicherung Selbstbehalt', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],
            ['name' => 'Krankenversicherung Zusatz', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],
            ['name' => 'Zahnzusatzversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],
            ['name' => 'Lebensversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],
            ['name' => 'Unfallversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],
            ['name' => 'Berufsunfähigkeitsversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],
            ['name' => 'Sterbegeldversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],
            ['name' => 'Pensionsvorsorge privat', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Personenversicherungen'],

            // Ausgaben - Versicherungen Sachen
            ['name' => 'Haushaltsversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Sachversicherungen'],
            ['name' => 'Haftpflichtversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Sachversicherungen'],
            ['name' => 'Rechtsschutzversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Sachversicherungen'],
            ['name' => 'Glasbruchversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Sachversicherungen'],
            ['name' => 'Gebäudeversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Sachversicherungen'],
            ['name' => 'Elementarversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Sachversicherungen'],
            ['name' => 'Reiseversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Versicherungen - Sachversicherungen'],

            // Ausgaben - Ernährung Lebensmittel
            ['name' => 'Supermarkt', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Lebensmittelgeschäft', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Bäcker', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Fleischer', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Metzger', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Wochenmarkt', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Bio-Laden', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Getränke Wasser', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Getränke Säfte', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],
            ['name' => 'Alkohol für zuhause', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Lebensmittel'],

            // Ausgaben - Ernährung Außer-Haus
            ['name' => 'Restaurants', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Cafés', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Fast Food', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Imbiss', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Würstelstand', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Kantine', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Mensa', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Lieferdienste', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Lieferando', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Mjam', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Bars', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],
            ['name' => 'Kneipen', 'category' => 'Ausgaben', 'subcategory' => 'Ernährung - Außer-Haus-Verpflegung'],

            // Ausgaben - Kleidung Bekleidung
            ['name' => 'Kleidung Damen', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Kleidung Herren', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Kinderkleidung', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Schuhe', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Sportbekleidung', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Unterwäsche', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Accessoires', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Taschen', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Gürtel', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Schmuck', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Winterkleidung', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Jacken', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Mäntel', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Bademode', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Textilreinigung', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Wäscherei', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],
            ['name' => 'Schuh-Reparatur', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Bekleidung'],

            // Ausgaben - Kleidung Körperpflege
            ['name' => 'Friseur', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Barbier', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Kosmetikstudio', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Nagelpflege', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Maniküre', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Hygieneartikel', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Shampoo', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Duschgel', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Make-up', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Kosmetik', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Parfum', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Deo', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Rasierbedarf', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Hautpflege', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Sonnenschutz', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Zahnpflege', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Zahnbürste', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],
            ['name' => 'Zahnpasta', 'category' => 'Ausgaben', 'subcategory' => 'Kleidung - Körperpflege & Kosmetik'],

            // Ausgaben - Gesundheit Medizin
            ['name' => 'Arztbesuche Selbstbehalt', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Rezeptgebühren', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Medikamente rezeptfrei', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Apotheke', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Zahnarzt', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Kieferorthopäde', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Physiotherapie', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Psychotherapie', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Heilpraktiker', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Osteopathie', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Chiropraktiker', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Akupunktur', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],
            ['name' => 'Massagen medizinisch', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Medizinische Versorgung'],

            // Ausgaben - Gesundheit Vorsorge
            ['name' => 'Vitamine', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],
            ['name' => 'Nahrungsergänzungsmittel', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],
            ['name' => 'Sportmedizinische Untersuchungen', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],
            ['name' => 'Impfungen', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],
            ['name' => 'Brillen', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],
            ['name' => 'Kontaktlinsen', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],
            ['name' => 'Hörgeräte', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],
            ['name' => 'Gesundheitschecks', 'category' => 'Ausgaben', 'subcategory' => 'Gesundheit - Gesundheitsvorsorge'],

            // Ausgaben - Bildung
            ['name' => 'Studiengebühren', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Schulbücher', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Schulmaterialien', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Nachhilfe', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Privatschule', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Internat', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Kinderbetreuung', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Kindergarten', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Tagesmutter', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Hort', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Ausbildung'],
            ['name' => 'Kurse', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'Seminare', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'Fachbücher', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'E-Learning', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'Online-Kurse', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'Zertifizierungen', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'Sprachkurse', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'Fachliteratur', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],
            ['name' => 'Zeitschriftenabos Fachmagazine', 'category' => 'Ausgaben', 'subcategory' => 'Bildung - Weiterbildung'],

            // Ausgaben - Kinder
            ['name' => 'Windeln', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Babynahrung', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Babykleidung', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Kinderwagen', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Babybett', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Babyausstattung', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Spielzeug', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Taschengeld', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Grundbedürfnisse'],
            ['name' => 'Sportvereine für Kinder', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Freizeit & Entwicklung'],
            ['name' => 'Musikunterricht', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Freizeit & Entwicklung'],
            ['name' => 'Hobbykurse', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Freizeit & Entwicklung'],
            ['name' => 'Kindergeburtstage', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Freizeit & Entwicklung'],
            ['name' => 'Ferienlager', 'category' => 'Ausgaben', 'subcategory' => 'Kinder - Freizeit & Entwicklung'],

            // Ausgaben - Freizeit Medien
            ['name' => 'Netflix', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Amazon Prime', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Disney+', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Spotify', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Apple Music', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'YouTube Premium', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Audible', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Kindle Unlimited', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'PlayStation Plus', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Xbox Game Pass', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Zeitungen', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Zeitschriften', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],
            ['name' => 'Bücher', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Medien & Streaming'],

            // Ausgaben - Freizeit Hobbies
            ['name' => 'Sportverein', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Fitnessstudio', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Yoga', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Pilates', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Kletterhalle', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Schwimmbad', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Therme', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Skilift-Tickets', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Ski-Ausrüstung', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Musikinstrumente', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Fotografieausrüstung', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Bastelmaterial', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Gaming Konsolen', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Gaming Spiele', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Gaming PC-Hardware', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Outdoor-Ausrüstung', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Angel-Ausrüstung', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],
            ['name' => 'Mitgliedsbeiträge Vereine', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Hobbies'],

            // Ausgaben - Freizeit Kultur
            ['name' => 'Kino', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Theater', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Konzerte', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Festivals', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Museen', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Ausstellungen', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Veranstaltungen', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Partys', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],
            ['name' => 'Clubbesuche', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Kultur & Ausgehen'],

            // Ausgaben - Freizeit Urlaub
            ['name' => 'Urlaubsreisen', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Wochenendtrips', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Hotel', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Unterkunft', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Flüge', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Mietwagen', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Reiseausrüstung', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Koffer', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Rucksäcke', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Reiseführer', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],
            ['name' => 'Souvenirs', 'category' => 'Ausgaben', 'subcategory' => 'Freizeit - Urlaub & Reisen'],

            // Ausgaben - Technologie Hardware
            ['name' => 'Smartphone', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Laptop', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Computer', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Tablet', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Smartwatch', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Kopfhörer', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Kamera', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'TV-Gerät', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Spielekonsole', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Smart Home Geräte', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],
            ['name' => 'Drucker', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Hardware'],

            // Ausgaben - Technologie Software
            ['name' => 'Handy-Vertrag', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'Prepaid-Guthaben', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'iCloud', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'Google Drive', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'Cloud-Speicher', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'Software-Lizenzen', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'Antivirenprogramme', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'VPN-Dienste', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],
            ['name' => 'App-Käufe', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Software & Services'],

            // Ausgaben - Technologie Reparatur
            ['name' => 'Handyreparatur', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Reparatur & Wartung'],
            ['name' => 'Computer-Reparatur', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Reparatur & Wartung'],
            ['name' => 'Software-Support', 'category' => 'Ausgaben', 'subcategory' => 'Technologie - Reparatur & Wartung'],

            // Ausgaben - Haustiere
            ['name' => 'Futter Haustier', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Tierarzt', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Tierkrankenversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Spielzeug Haustier', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Pflegeprodukte Haustier', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Hundefriseur', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Hundeschule', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Katzenstreu', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Aquarium', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Terrarium', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Tierbetreuung', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Tierpension', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Hundehaftpflichtversicherung', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],
            ['name' => 'Hundesteuer', 'category' => 'Ausgaben', 'subcategory' => 'Haustiere'],

            // Ausgaben - Soziales
            ['name' => 'Geburtstaggeschenke', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Weihnachtsgeschenke', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Hochzeitsgeschenke', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Taufgeschenke', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Valentinstag', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Muttertag', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Vatertag', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Blumen', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Grußkarten', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Spenden', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Vereinsbeiträge', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Kirchenbeitrag', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],
            ['name' => 'Patenschaftsbeiträge', 'category' => 'Ausgaben', 'subcategory' => 'Soziales & Geschenke'],

            // Ausgaben - Finanzen Sparen
            ['name' => 'Sparplan', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'Fondssparplan', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'ETF-Sparplan', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'Aktien', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'Kryptowährungen', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'Bausparer', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'Festgeld', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'Gold', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],
            ['name' => 'Edelmetalle', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Sparen & Investieren'],

            // Ausgaben - Finanzen Kredite
            ['name' => 'Konsumkredit', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Kredite & Schulden'],
            ['name' => 'Studienkredit', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Kredite & Schulden'],
            ['name' => 'Kreditkartenrückzahlung', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Kredite & Schulden'],
            ['name' => 'Ratenzahlungen', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Kredite & Schulden'],
            ['name' => 'Dispozinsen', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Kredite & Schulden'],
            ['name' => 'Mahngebühren', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Kredite & Schulden'],

            // Ausgaben - Finanzen Bankgebühren
            ['name' => 'Kontoführungsgebühren', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Bankgebühren'],
            ['name' => 'Kreditkartengebühren', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Bankgebühren'],
            ['name' => 'Überweisungsgebühren', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Bankgebühren'],
            ['name' => 'Geldautomatengebühren', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Bankgebühren'],

            // Ausgaben - Finanzen Vorsorge
            ['name' => 'Notfallreserve', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Vorsorge'],
            ['name' => 'Altersvorsorge', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Vorsorge'],
            ['name' => 'Reparaturrücklage', 'category' => 'Ausgaben', 'subcategory' => 'Finanzen - Vorsorge'],

            // Ausgaben - Rechtliches
            ['name' => 'Anwaltskosten', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Gerichtskosten', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Notarkosten', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Steuerberater', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Steuernachzahlungen', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Bußgelder', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Strafen', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Meldegebühren', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Pass', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Personalausweis', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Führerschein', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Visa-Gebühren', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],
            ['name' => 'Beglaubigungen', 'category' => 'Ausgaben', 'subcategory' => 'Rechtliches & Behörden'],

            // Ausgaben - Sonstiges Unterhalt
            ['name' => 'Unterhalt für Kinder gezahlt', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Unterhaltszahlungen'],
            ['name' => 'Unterhalt für Ex-Partner gezahlt', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Unterhaltszahlungen'],

            // Ausgaben - Sonstiges Dienste
            ['name' => 'Putzfrau', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],
            ['name' => 'Reinigungskraft', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],
            ['name' => 'Gärtner', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],
            ['name' => 'Handwerker', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],
            ['name' => 'Umzugskosten', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],
            ['name' => 'Lagerraum', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],
            ['name' => 'Selfstorage', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],
            ['name' => 'Schließfach', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Persönliche Dienstleistungen'],

            // Ausgaben - Sonstiges Notfälle
            ['name' => 'Notfälle', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Unvorhergesehenes'],
            ['name' => 'Notfall-Reparaturen', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Unvorhergesehenes'],
            ['name' => 'Unerwartete Ausgaben', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Unvorhergesehenes'],

            // Ausgaben - Sonstiges Laster
            ['name' => 'Zigaretten', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Laster & Genussmittel'],
            ['name' => 'Glücksspiel', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Laster & Genussmittel'],
            ['name' => 'Wetten', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Laster & Genussmittel'],
            ['name' => 'Lotto', 'category' => 'Ausgaben', 'subcategory' => 'Sonstiges - Laster & Genussmittel'],
        ];

        foreach ($categories as $category) {
            BudgetCategory::firstOrCreate(
                ['name' => $category['name']],
                [
                    'category' => $category['category'],
                    'subcategory' => $category['subcategory'],
                ]
            );
        }
    }
}
