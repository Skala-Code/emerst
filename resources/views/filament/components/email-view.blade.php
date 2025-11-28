<div class="space-y-4">
    <div>
        <strong>Assunto:</strong> {{ $email->subject }}
    </div>
    
    <div>
        <strong>De:</strong> {{ $email->from_name }} &lt;{{ $email->from_email }}&gt;
    </div>
    
    <div>
        <strong>Recebido em:</strong> {{ $email->received_at->format('d/m/Y H:i') }}
    </div>
    
    @if($email->to)
        <div>
            <strong>Para:</strong> 
            @foreach($email->to as $to)
                {{ $to['name'] ?? $to['email'] }}@if(!$loop->last), @endif
            @endforeach
        </div>
    @endif
    
    <div class="border-t pt-4">
        <div class="prose max-w-none">
            {!! $email->body_html ?? nl2br(e($email->body_text)) !!}
        </div>
    </div>
</div>

