<form method="POST" action="#" class="space-y-4">
    <h3 class="text-lg font-semibold">Configuração do Mandato</h3>
    <div class="space-y-2">
        <label class="form-label" for="mandate_start">Data de início do mandato</label>
        <input id="mandate_start" name="mandate_start" type="date" class="form-control" required>
    </div>
    <div class="space-y-2">
        <label class="form-label" for="mandate_duration">Tempo de mandato (em anos)</label>
        <input id="mandate_duration" name="mandate_duration" type="number" min="1" max="10" class="form-control" required>
    </div>
    <button type="submit" class="btn">Salvar mandato</button>
</form>
