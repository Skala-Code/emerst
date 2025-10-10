<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsolidatedReportResource\Pages;
use App\Models\ServiceOrder;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ConsolidatedReportResource extends Resource
{
    protected static ?string $model = ServiceOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Relatórios Consolidados';

    protected static ?string $navigationGroup = 'Relatórios';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Nº OS')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('process.number')
                    ->label('Nº Processo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('process.trt')
                    ->label('TRT')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('process.classe')
                    ->label('Classe')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('workflow_stage')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'gray',
                        'assigned' => 'warning',
                        'in_progress' => 'info',
                        'review' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Criada',
                        'assigned' => 'Atribuída',
                        'in_progress' => 'Em Andamento',
                        'review' => 'Em Revisão',
                        'completed' => 'Concluída',
                        'rejected' => 'Rejeitada',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('has_liquidation')
                    ->label('Liquidação')
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! empty($record->liquidation_numero_calculo))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('has_report')
                    ->label('Relatório')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->reports()->count() > 0)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trt')
                    ->label('TRT')
                    ->options(function () {
                        return \App\Models\Process::select('trt')
                            ->distinct()
                            ->whereNotNull('trt')
                            ->orderBy('trt')
                            ->pluck('trt', 'trt');
                    })
                    ->query(function (Builder $query, $state) {
                        if ($state['value']) {
                            $query->whereHas('process', function ($q) use ($state) {
                                $q->where('trt', $state['value']);
                            });
                        }
                    })
                    ->searchable()
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Criado de')
                            ->placeholder('dd/mm/aaaa'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Criado até')
                            ->placeholder('dd/mm/aaaa'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Criado de ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y'))
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Criado até ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y'))
                                ->removeField('created_until');
                        }

                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('workflow_stage')
                    ->label('Status')
                    ->options([
                        'created' => 'Criada',
                        'assigned' => 'Atribuída',
                        'in_progress' => 'Em Andamento',
                        'review' => 'Em Revisão',
                        'completed' => 'Concluída',
                        'rejected' => 'Rejeitada',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('has_liquidation')
                    ->label('Com Liquidação')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('liquidation_numero_calculo'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_report')
                    ->label('Com Relatório')
                    ->query(fn (Builder $query): Builder => $query->has('reports'))
                    ->toggle(),
            ])
            ->actions([
                // Sem ações individuais
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('generate_consolidated_report')
                        ->label('Gerar Relatório Consolidado')
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Redirecionar para página de visualização do relatório
                            $ids = $records->pluck('id')->toArray();
                            return redirect()->route('filament.admin.resources.consolidated-reports.view-report', [
                                'ids' => implode(',', $ids)
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsolidatedReports::route('/'),
            'view-report' => Pages\ViewConsolidatedReport::route('/report'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
