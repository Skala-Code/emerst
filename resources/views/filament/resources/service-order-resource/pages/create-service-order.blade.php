<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <form wire:submit="create">
                {{ $this->form }}
                
                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </form>
        </div>
        
        <div class="lg:col-span-1">
            @if($this->linkedEmail)
                <x-filament::section>
                    <x-slot name="heading">
                        Email Vinculado
                    </x-slot>
                    
                    <div class="space-y-4">
                        <div>
                            <strong>Assunto:</strong> {{ $this->linkedEmail->subject }}
                        </div>
                        
                        <div>
                            <strong>De:</strong> {{ $this->linkedEmail->from_name }} &lt;{{ $this->linkedEmail->from_email }}&gt;
                        </div>
                        
                        <div>
                            <strong>Recebido em:</strong> {{ $this->linkedEmail->received_at->format('d/m/Y H:i') }}
                        </div>
                        
                        <div class="border-t pt-4 max-h-96 overflow-y-auto">
                            <div class="prose max-w-none text-sm">
                                {!! $this->linkedEmail->body_html ?? nl2br(e($this->linkedEmail->body_text)) !!}
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>

