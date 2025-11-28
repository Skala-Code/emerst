<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            {{ $this->table }}
        </div>
        
        <div class="lg:col-span-1">
            @if($this->selectedEmail)
                <x-filament::section>
                    <x-slot name="heading">
                        {{ $this->selectedEmail->subject }}
                    </x-slot>
                    
                    <div class="space-y-4">
                        <div>
                            <strong>De:</strong> {{ $this->selectedEmail->from_name }} &lt;{{ $this->selectedEmail->from_email }}&gt;
                        </div>
                        
                        <div>
                            <strong>Recebido em:</strong> {{ $this->selectedEmail->received_at->format('d/m/Y H:i') }}
                        </div>
                        
                        @if($this->selectedEmail->to)
                            <div>
                                <strong>Para:</strong> 
                                @foreach($this->selectedEmail->to as $to)
                                    {{ $to['name'] ?? $to['email'] }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="border-t pt-4">
                            <div class="prose max-w-none">
                                {!! $this->selectedEmail->body_html ?? nl2br(e($this->selectedEmail->body_text)) !!}
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <x-slot name="heading">
                        Visualizar Email
                    </x-slot>
                    
                    <p class="text-gray-500">Selecione um email para visualizar</p>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>

