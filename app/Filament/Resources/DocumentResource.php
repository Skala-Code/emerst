<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Documentos';

    protected static ?string $navigationGroup = 'Documentos';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_documents') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3),
                Forms\Components\Select::make('category')
                    ->label('Categoria')
                    ->options([
                        'petição' => 'Petição',
                        'contrato' => 'Contrato',
                        'certidão' => 'Certidão',
                        'procuração' => 'Procuração',
                        'sentença' => 'Sentença',
                        'recurso' => 'Recurso',
                        'parecer' => 'Parecer',
                        'outros' => 'Outros',
                    ]),
                Forms\Components\Toggle::make('is_public')
                    ->label('Documento Público'),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Data de Expiração'),
                Forms\Components\FileUpload::make('file_path')
                    ->label('Arquivo')
                    ->disk('private')
                    ->directory('documents')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/*', 'text/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(10240), // 10MB
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_name')
                    ->label('Arquivo Original')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documentable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\Process' => 'Processo',
                        'App\Models\ServiceOrder' => 'Ordem de Serviço',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'petição' => 'primary',
                        'contrato' => 'success',
                        'certidão' => 'warning',
                        'procuração' => 'info',
                        'sentença' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('file_size_formatted')
                    ->label('Tamanho'),
                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('Enviado por'),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Público')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'petição' => 'Petição',
                        'contrato' => 'Contrato',
                        'certidão' => 'Certidão',
                        'procuração' => 'Procuração',
                        'sentença' => 'Sentença',
                        'recurso' => 'Recurso',
                        'parecer' => 'Parecer',
                        'outros' => 'Outros',
                    ]),
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Documento Público'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Document $record): string => route('documents.view', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Document $record): string => route('documents.download', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Document $record) {
                        // Delete the physical file
                        if (Storage::disk('private')->exists($record->file_path)) {
                            Storage::disk('private')->delete($record->file_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Delete physical files
                            foreach ($records as $record) {
                                if (Storage::disk('private')->exists($record->file_path)) {
                                    Storage::disk('private')->delete($record->file_path);
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
