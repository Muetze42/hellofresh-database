<?php

namespace App\Livewire\Portal\Docs;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
abstract class AbstractEndpointDoc extends Component
{
    abstract protected function title(): string;

    abstract protected function description(): string;

    /**
     * @return array<int, array{method: string, path: string, description?: string}>
     */
    abstract protected function endpoints(): array;

    /**
     * @return array<int, array{name: string, type: string, description: string}>
     */
    protected function queryParams(): array
    {
        return [];
    }

    /**
     * @return array<int, array{name: string, type: string, description: string}>
     */
    abstract protected function responseFields(): array;

    abstract protected function exampleRequest(): string;

    public function render(): View
    {
        return view('portal::livewire.docs.endpoint', [
            'title' => $this->title(),
            'description' => $this->description(),
            'endpoints' => $this->endpoints(),
            'queryParams' => $this->queryParams(),
            'responseFields' => $this->responseFields(),
            'exampleRequest' => $this->exampleRequest(),
        ])->title($this->title());
    }
}
