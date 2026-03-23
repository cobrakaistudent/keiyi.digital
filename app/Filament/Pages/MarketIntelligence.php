<?php

namespace App\Filament\Pages;

use App\Models\BusinessCost;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\MarketResearchRequest;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MarketIntelligence extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Inteligencia de Mercado';

    protected static ?string $navigationGroup = 'Economía';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Inteligencia de Mercado';

    protected static string $view = 'filament.pages.market-intelligence';

    public ?string $request_title = '';

    public ?string $request_purpose = '';

    public ?string $request_target_market = '';

    public ?string $request_priority = 'normal';

    public function requestForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('request_title')
                    ->label('Tema del estudio')
                    ->placeholder('Ej: Competidores de impresión 3D en CDMX')
                    ->required()
                    ->maxLength(255),
                Textarea::make('request_purpose')
                    ->label('Propósito / Qué necesitas saber')
                    ->placeholder('Ej: Necesito entender quiénes son los competidores directos, sus precios, y qué servicios ofrecen para definir nuestra estrategia de diferenciación.')
                    ->required()
                    ->rows(3),
                TextInput::make('request_target_market')
                    ->label('Mercado objetivo')
                    ->placeholder('Ej: México, LATAM, global...')
                    ->maxLength(255),
                Select::make('request_priority')
                    ->label('Prioridad')
                    ->options([
                        'low' => 'Baja — cuando se pueda',
                        'normal' => 'Normal — esta semana',
                        'high' => 'Alta — lo antes posible',
                        'urgent' => 'Urgente — hoy',
                    ])
                    ->default('normal'),
            ])
            ->statePath('');
    }

    public function submitRequest(): void
    {
        $this->requestForm->getState();

        MarketResearchRequest::create([
            'title' => $this->request_title,
            'purpose' => $this->request_purpose,
            'target_market' => $this->request_target_market,
            'priority' => $this->request_priority,
            'requested_by' => auth()->id(),
        ]);

        $this->request_title = '';
        $this->request_purpose = '';
        $this->request_target_market = '';
        $this->request_priority = 'normal';

        Notification::make()
            ->title('Solicitud enviada')
            ->body('Tu estudio de mercado ha sido registrado.')
            ->success()
            ->send();
    }

    public function getViewData(): array
    {
        $monthlyCost = BusinessCost::active()->get()->sum(fn ($c) => $c->monthly_cost);
        $totalCourses = Course::where('is_published', true)->count();
        $totalLessons = Lesson::where('is_published', true)->count();
        $totalStudents = User::where('role', 'student')->count();
        $approvedStudents = User::where('role', 'student')->where('approval_status', 'approved')->count();

        $requests = MarketResearchRequest::latest()->get();

        return [
            'monthlyCost' => $monthlyCost,
            'totalCourses' => $totalCourses,
            'totalLessons' => $totalLessons,
            'totalStudents' => $totalStudents,
            'approvedStudents' => $approvedStudents,
            'breakeven_general' => $monthlyCost > 0 ? ceil($monthlyCost / 199.99) : 0,
            'breakeven_student' => $monthlyCost > 0 ? ceil($monthlyCost / 49.99) : 0,
            'requests' => $requests,
        ];
    }
}
