<?php
namespace App\Filament\Resources;
use App\Models\Inquiry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, Textarea, Toggle};
use Filament\Tables\Columns\{TextColumn, IconColumn};
use Filament\Actions\{EditAction, DeleteAction};
class InquiryResource extends Resource {
    protected static ?string $model = Inquiry::class;
    protected static ?string $modelLabel = 'zapytanie';
    protected static ?string $pluralModelLabel = 'Zapytania';
    public static function canCreate(): bool { return false; }
    public static function form(Schema $schema): Schema {
        return $schema->components([
            TextInput::make('name')->label('Imię')->disabled(),
            TextInput::make('email')->label('E-mail')->disabled(),
            TextInput::make('company')->label('Firma')->disabled(),
            TextInput::make('phone')->label('Telefon')->disabled(),
            Textarea::make('message')->label('Treść')->disabled()->rows(10),
            Toggle::make('is_read')->label('Przeczytane'),
        ]);
    }
    public static function table(Table $table): Table {
        return $table->columns([
            TextColumn::make('created_at')->label('Data')->dateTime('d.m.Y H:i')->sortable(),
            TextColumn::make('name')->label('Imię')->searchable(),
            TextColumn::make('email')->label('E-mail')->searchable(),
            IconColumn::make('is_read')->label('Przeczytane')->boolean(),
        ])->defaultSort('created_at','desc')->recordActions([EditAction::make()->label('Otwórz'),DeleteAction::make()]);
    }
    public static function getPages(): array {
        return ['index'=>InquiryResource\Pages\ListInquiries::route('/'), 'edit'=>InquiryResource\Pages\EditInquiry::route('/{record}/edit')];
    }
}
