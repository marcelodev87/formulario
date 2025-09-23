<x-mail::message>
# Olá {{ $recipientName ?: "Presidente" }}

Recebemos sua solicitação de acesso ao Formulário de Abertura.
Clique no botão abaixo para continuar o processo. O link expira em 30 minutos e só pode ser utilizado uma vez.

<x-mail::button :url="$loginUrl">
Acessar o formulário
</x-mail::button>

Se você não solicitou este e-mail, ignore-o.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>