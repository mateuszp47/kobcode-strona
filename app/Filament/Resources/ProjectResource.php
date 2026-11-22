<?php
namespace App\Filament\Resources;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, Textarea, Select, Toggle, FileUpload, Repeater, TagsInput, RichEditor};
use Filament\Tables\Columns\{TextColumn, IconColumn};
use Filament\Actions\{EditAction, DeleteAction};
class ProjectResource extends Resource {
    protected static ?string $model = Project::class;
    protected static ?string $modelLabel = 'realizacja';
    protected static ?string $pluralModelLabel = 'Realizacje';
    public static function form(Schema $schema): Schema {
        return $schema->components([
            TextInput::make('title')->label('Tytuł')->required()->maxLength(255),
            TextInput::make('slug')->label('Adres URL')->required()->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord:true)->maxLength(255),
            TextInput::make('category')->label('Rodzaj rozwiązania')->required()->maxLength(255),
            Textarea::make('summary')->label('Krótki opis')->required()->maxLength(1000),
            Select::make('relationship')->label('Relacja z projektem')->options(['unverified'=>'Do ustalenia','portfolio'=>'Realizacja KOBCODE — bez określenia relacji','related'=>'Firma powiązana','own'=>'Projekt własny','client'=>'Projekt dla klienta'])->required(),
            Select::make('delivery_status')->label('Stan projektu')->options(['unverified'=>'Do potwierdzenia','in_development'=>'W rozwoju','deployed'=>'Wdrożony'])->required(),
            RichEditor::make('goal')->toolbarButtons(['bold', 'italic', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])->label('Cel projektu')->maxLength(10000),
            RichEditor::make('problem')->toolbarButtons(['bold', 'italic', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])->label('Problem')->maxLength(10000),
            RichEditor::make('scope')->toolbarButtons(['bold', 'italic', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])->label('Zakres naszej pracy')->maxLength(10000),
            RichEditor::make('solution')->toolbarButtons(['bold', 'italic', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])->label('Rozwiązanie')->maxLength(10000),
            RichEditor::make('result')->toolbarButtons(['bold', 'italic', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])->label('Potwierdzony rezultat')->maxLength(10000),
            FileUpload::make('cover')->label('Zatwierdzone zdjęcie')->disk('public')->directory('portfolio')->image()->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->maxSize(4096)->visibility('public'),
            TextInput::make('cover_alt')->label('Opis zdjęcia')->requiredWith('cover')->maxLength(255),
            TagsInput::make('technologies')->label('Potwierdzone technologie')->helperText('Dodawaj wyłącznie technologie faktycznie użyte w projekcie.'),
            Select::make('service_links')->label('Powiązane usługi')->multiple()->options(collect(config('offer'))->mapWithKeys(fn ($offer, $name) => [$name => $offer['name']])->all()),
            Repeater::make('gallery')->label('Galeria realizacji')->schema([
                FileUpload::make('path')->label('Zdjęcie lub zrzut')->disk('public')->directory('portfolio')->image()->acceptedFileTypes(['image/jpeg','image/png','image/webp'])->maxSize(4096)->visibility('public')->required(),
                TextInput::make('alt')->label('Opis alternatywny zdjęcia')->required()->maxLength(255),
                TextInput::make('caption')->label('Podpis (opcjonalnie)')->maxLength(500),
            ])->defaultItems(0)->maxItems(12)->reorderable()->helperText('Używaj zatwierdzonych materiałów. Widoki demonstracyjne oznacz w podpisie. Usunięcie pozycji z galerii nie usuwa pliku z dysku.'),
            TextInput::make('seo_title')->label('Tytuł SEO')->maxLength(255),
            Textarea::make('seo_description')->label('Opis SEO')->maxLength(320),
            TextInput::make('sort_order')->label('Kolejność')->numeric()->minValue(0)->default(0)->required(),
            Toggle::make('approved_for_publication')->label('Potwierdzono autorstwo, treści i prawa do materiałów')->default(false),
            Toggle::make('is_published')->label('Opublikuj')->helperText('Publikacja wymaga powyższego potwierdzenia i ustalenia relacji z projektem. Zgoda na markę nie potwierdza wdrożenia wszystkich funkcji.')->default(false),
        ]);
    }
    public static function table(Table $table): Table {
        return $table->columns([
            TextColumn::make('title')->label('Tytuł')->searchable(),
            TextColumn::make('category')->label('Rodzaj'),
            IconColumn::make('is_published')->label('Publikacja')->boolean(),
            IconColumn::make('approved_for_publication')->label('Zatwierdzono')->boolean(),
        ])->defaultSort('sort_order')->recordActions([EditAction::make(), DeleteAction::make()]);
    }
    public static function getPages(): array {
        return ['index'=>ProjectResource\Pages\ListProjects::route('/'), 'create'=>ProjectResource\Pages\CreateProject::route('/create'), 'edit'=>ProjectResource\Pages\EditProject::route('/{record}/edit')];
    }
}
