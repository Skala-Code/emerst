<?php

namespace App\Filament\Resources\ProcessResource\Pages;

use App\Filament\Resources\ProcessResource;
use Filament\Resources\Pages\Page;

class CreateProcessSimple extends Page
{
    protected static string $resource = ProcessResource::class;

    protected static string $view = 'filament.resources.process-resource.pages.create-process-simple';
}
