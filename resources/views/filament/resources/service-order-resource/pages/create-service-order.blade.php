<x-filament-panels::page>
    <div class="space-y-4">
        @if($this->linkedEmail)
            <x-filament::section class="!mb-4">
                <x-slot name="heading">
                    Email Vinculado
                </x-slot>
                
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Assunto:</strong>
                            <div class="mt-1 text-gray-900 dark:text-gray-100 break-words">{{ $this->linkedEmail->subject }}</div>
                        </div>
                        
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">De:</strong>
                            <div class="mt-1 text-gray-900 dark:text-gray-100 break-words">
                                {{ $this->linkedEmail->from_name }} &lt;{{ $this->linkedEmail->from_email }}&gt;
                            </div>
                        </div>
                        
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Recebido em:</strong>
                            <div class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $this->linkedEmail->received_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    <details class="email-body-details border-t pt-3 mt-3">
                        <summary class="cursor-pointer text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium flex items-center justify-between list-none">
                            <span>Ver corpo do email</span>
                            <svg 
                                class="w-4 h-4 transition-transform email-details-arrow"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </summary>
                        <div class="mt-3">
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                <div class="max-h-[400px] overflow-y-auto p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    {!! $this->linkedEmail->body_html ?? nl2br(e($this->linkedEmail->body_text)) !!}
                                </div>
                            </div>
                        </div>
                    </details>
                </div>
            </x-filament::section>
        @endif
        
        <form wire:submit="create" class="w-full">
            {{ $this->form }}
            
            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </form>
    </div>
    
    <style>
        .email-body-details summary::-webkit-details-marker {
            display: none;
        }
        
        .email-body-details[open] .email-details-arrow {
            transform: rotate(180deg);
        }
        
        .email-body-details summary::marker {
            display: none;
        }
    </style>
</x-filament-panels::page>

