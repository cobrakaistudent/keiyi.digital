<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Academia';

    protected static ?string $navigationLabel = 'Avance de Alumnos';

    protected static ?string $modelLabel = 'Inscripción';

    protected static ?string $pluralModelLabel = 'Inscripciones';

    protected static ?int $navigationSort = 20;

    public static function getNavigationBadge(): ?string
    {
        return (string) Enrollment::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Alumno')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, Enrollment $record) {
                        $user = $record->user;
                        if (! $user) {
                            return 'Usuario eliminado';
                        }
                        $full = $user->name;
                        if ($user->apellido_paterno) {
                            $full .= ' '.$user->apellido_paterno;
                        }

                        return $full;
                    }),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('course_id')
                    ->label('Curso')
                    ->formatStateUsing(function (string $state) {
                        $course = Course::where('slug', $state)->first();

                        return $course
                            ? ($course->emoji.' '.$course->title)
                            : $state;
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Avance')
                    ->suffix('%')
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'warning',
                        $state > 0 => 'info',
                        default => 'gray',
                    })
                    ->badge(),

                Tables\Columns\TextColumn::make('lessons_completed')
                    ->label('Lecciones')
                    ->getStateUsing(function (Enrollment $record): string {
                        $course = Course::where('slug', $record->course_id)->first();
                        if (! $course) {
                            return '—';
                        }
                        $total = $course->publishedLessons()->count();
                        $completed = LessonCompletion::where('user_id', $record->user_id)
                            ->whereIn('lesson_id', $course->publishedLessons()->pluck('lessons.id'))
                            ->count();

                        return "{$completed}/{$total}";
                    }),

                Tables\Columns\TextColumn::make('enrolled_at')
                    ->label('Inscrito')
                    ->dateTime('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última actividad')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('enrolled_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Curso')
                    ->options(fn () => Course::published()->pluck('title', 'slug')->toArray()),

                Tables\Filters\SelectFilter::make('progress')
                    ->label('Estado de avance')
                    ->options([
                        'not_started' => 'Sin comenzar (0%)',
                        'in_progress' => 'En progreso (1-99%)',
                        'completed' => 'Completado (100%)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'not_started' => $query->where('progress_percent', 0),
                            'in_progress' => $query->where('progress_percent', '>', 0)->where('progress_percent', '<', 100),
                            'completed' => $query->where('progress_percent', '>=', 100),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('ver_detalle')
                    ->label('Detalle')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Enrollment $record): string => 'Detalle de avance')
                    ->modalContent(function (Enrollment $record) {
                        $course = Course::where('slug', $record->course_id)->first();
                        if (! $course) {
                            return view('filament.modals.enrollment-detail', ['lessons' => collect(), 'record' => $record, 'course' => null]);
                        }
                        $lessons = $course->publishedLessons;
                        $completions = LessonCompletion::where('user_id', $record->user_id)
                            ->whereIn('lesson_id', $lessons->pluck('id'))
                            ->get()
                            ->keyBy('lesson_id');

                        return view('filament.modals.enrollment-detail', [
                            'record' => $record,
                            'course' => $course,
                            'lessons' => $lessons,
                            'completions' => $completions,
                        ]);
                    })
                    ->modalWidth('lg'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
