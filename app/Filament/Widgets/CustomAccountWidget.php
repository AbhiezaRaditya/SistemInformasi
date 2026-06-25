<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CustomAccountWidget extends Widget
{
    protected string $view = 'filament.widgets.custom-account-widget';

    protected int|string|array $columnSpan = 'full';
}