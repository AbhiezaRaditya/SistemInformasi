<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Umum')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('pengurus_unit.name')
                            ->label('Nama Pengurus'),

                        TextEntry::make('unit.codename')
                            ->label('Unit'),

                        TextEntry::make('title')
                            ->label('Judul Aktivitas')
                            ->columnSpanFull(),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail Kegiatan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('category.name')
                            ->label('Kategori Kegiatan'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(function ($state, $record) {
                                if ($state === 'pending' && filled($record->realization_file)) {
                                    return 'Dalam Realisasi';
                                }

                                return match ($state) {
                                    'draft' => 'Draft',
                                    'pending' => 'Pending',
                                    'revisi' => 'Revisi',
                                    'reject' => 'Ditolak',
                                    'dalam_realisasi' => 'Dalam Realisasi',
                                    'completed' => 'Selesai',
                                    default => $state,
                                };
                            })
                            ->color(function ($state, $record) {
                                if ($state === 'pending' && filled($record->realization_file)) {
                                    return 'info';
                                }

                                return match ($state) {
                                    'draft'   => 'danger',
                                    'pending' => 'warning',
                                    'revisi'  => 'info',
                                    'accept'  => 'success',
                                    'reject'  => 'danger',
                                    'completed' => 'success',
                                    'dalam_realisasi' => 'info',
                                    default => 'gray',
                                };
                            }),

                        TextEntry::make('tanggal_berlangsung')
                            ->label('Tanggal Berlangsung')
                            ->date()
                            ->placeholder('-'),

                        TextEntry::make('tanggal_berakhir')
                            ->label('Tanggal Berakhir')
                            ->date()
                            ->placeholder('-'),

                        TextEntry::make('catatan_revisi')
                            ->label('Catatan Revisi')
                            ->html()
                            ->formatStateUsing(fn($state) => new HtmlString("
                                <div style='
                                    background: rgba(255, 235, 59, 0.20);
                                    border-left: 4px solid #facc15;
                                    padding: 12px;
                                    border-radius: 8px;
                                '>
                                    {$state}
                                </div>
                            "))
                            ->columnSpanFull()
                            ->visible(fn($record) => filled($record->catatan_revisi)),
                    ]),

                Section::make('Dokumen Lampiran')
                    ->schema([
                        TextEntry::make('attachment')
                            ->hiddenLabel()
                            ->html()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return '-';

                                $files = is_array($state) ? $state : [$state];

                                $htmlContent = collect($files)->map(function ($file) {
                                    $name = basename($file);
                                    $cleanName = preg_replace('/^[0-9a-zA-Z]{26}_/', '', $name);
                                    $url = asset('storage/' . $file);

                                    return '
                                    <div style="display: block; width: 100%; margin-bottom: 10px; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; background: #fff;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <span style="font-size: 0.875rem; color: #334155; font-weight: 500;">'.htmlspecialchars($cleanName).'</span>
                                            
                                            <a href="' . $url . '" download="' . htmlspecialchars($cleanName) . '" style="
                                                display: inline-flex; align-items: center; padding: 5px 12px;
                                                background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px;
                                                color: #475569; font-size: 0.75rem; font-weight: 600; text-decoration: none;
                                                transition: background 0.2s;
                                            " onmouseover="this.style.background=\'#f1f5f9\'" onmouseout="this.style.background=\'#f8fafc\'">Unduh Berkas</a>
                                        </div>
                                    </div>';
                                })->implode('');

                                return new HtmlString($htmlContent);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => filled($record->attachment)),

                Section::make('Dokumen Realisasi')
                    ->extraAttributes([
                        'class' => 'bg-success-50 dark:bg-success-400/10 rounded-xl',
                    ])
                    ->schema([
                        TextEntry::make('realization_file')
                            ->hiddenLabel()
                            ->html()
                            ->formatStateUsing(function ($state, $record) {
                                if (!$state) return '-';

                                $files = is_array($state) ? $state : [$state];
                                $index = 1;

                                $htmlContent = collect($files)->map(function ($file) use ($record, &$index) {
                                    $name = basename($file);
                                    $ext = pathinfo($name, PATHINFO_EXTENSION);

                                    if (preg_match('/^[0-9a-zA-Z]{26}_(.+)$/', $name, $matches)) {
                                        $cleanName = $matches[1];
                                    } elseif (preg_match('/^[a-zA-Z0-9]{20,}\.[a-zA-Z0-9]+$/', $name)) {
                                        $judul = $record->title ?? 'Dokumen Realisasi';
                                        $cleanName = 'Realisasi - ' . $judul . ' (' . $index . ').' . $ext;
                                    } else {
                                        $cleanName = $name;
                                    }

                                    $index++;
                                    $url = asset('storage/' . $file);

                                    return '
                                    <div style="display: block; width: 100%; margin-bottom: 10px; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; background: #fff;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <span style="font-size: 0.875rem; color: #334155; font-weight: 500;">'.htmlspecialchars($cleanName).'</span>
                                            
                                            <a href="' . $url . '" download="' . htmlspecialchars($cleanName) . '" style="
                                                display: inline-flex; align-items: center; padding: 5px 12px;
                                                background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px;
                                                color: #475569; font-size: 0.75rem; font-weight: 600; text-decoration: none;
                                                transition: background 0.2s;
                                            " onmouseover="this.style.background=\'#f1f5f9\'" onmouseout="this.style.background=\'#f8fafc\'">Unduh Berkas</a>
                                        </div>
                                    </div>';
                                })->implode('');

                                return new HtmlString($htmlContent);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => filled($record->realization_file)),

                Section::make('Timestamps')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime(timezone: 'Asia/Makassar')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime(timezone: 'Asia/Makassar')
                            ->placeholder('-'),

                        TextEntry::make('pending_at')
                            ->label('Pending')
                            ->formatStateUsing(fn($state) => 'Terakhir di kirim jam ' . $state->setTimezone('Asia/Makassar')->format('H:i, d F Y'))
                            ->placeholder('Belum pernah pending')
                            ->visible(fn($record) => filled($record->pending_at)),

                        TextEntry::make('revisi_at')
                            ->label('Revisi')
                            ->formatStateUsing(fn($state) => 'Terakhir direvisi jam ' . $state->setTimezone('Asia/Makassar')->format('H:i, d F Y'))
                            ->placeholder('Belum pernah revisi')
                            ->visible(fn($record) => filled($record->revisi_at)),

                        TextEntry::make('reject_at')
                            ->label('Ditolak')
                            ->formatStateUsing(fn($state) => 'Terakhir ditolak jam ' . $state->setTimezone('Asia/Makassar')->format('H:i, d F Y'))
                            ->placeholder('Belum pernah ditolak')
                            ->visible(fn($record) => filled($record->reject_at)),

                        TextEntry::make('realisasi_at')
                            ->label('Dalam Realisasi')
                            ->formatStateUsing(fn($state) => 'Mulai realisasi jam ' . $state->setTimezone('Asia/Makassar')->format('H:i, d F Y'))
                            ->placeholder('Belum masuk realisasi')
                            ->visible(fn($record) => filled($record->realisasi_at)),

                        TextEntry::make('completed_at')
                            ->label('Selesai')
                            ->formatStateUsing(fn($state) => 'Selesai jam ' . $state->setTimezone('Asia/Makassar')->format('H:i, d F Y'))
                            ->placeholder('Belum selesai')
                            ->visible(fn($record) => filled($record->completed_at)),
                    ]),
            ]);
    }
}