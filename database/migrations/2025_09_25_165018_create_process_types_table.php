<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('default_title');
            $table->string('cta_label')->default('Continuar processo');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        $types = [
            [
                'slug' => 'institution_opening',
                'name' => 'Abertura de Igreja',
                'default_title' => 'Processo de abertura',
                'cta_label' => 'Continuar cadastro',
                'description' => 'Cadastro completo da diretoria, endereco e dados administrativos.',
            ],
            [
                'slug' => 'board_election_minutes_registration',
                'name' => 'Registro de Ata de Eleicao da Diretoria',
                'default_title' => 'Registro de eleicao da diretoria',
                'cta_label' => 'Preencher ata',
                'description' => 'Documentacao para formalizar a eleicao da diretoria perante os orgaos competentes.',
            ],
            [
                'slug' => 'address_change_minutes_registration',
                'name' => 'Registro de Ata de Alteracao de Endereco',
                'default_title' => 'Alteracao de endereco',
                'cta_label' => 'Registrar alteracao',
                'description' => 'Processo para atualizar o endereco da instituicao em cartorio e demais registros.',
            ],
            [
                'slug' => 'bylaws_revision',
                'name' => 'Reforma de Estatuto',
                'default_title' => 'Reforma de estatuto social',
                'cta_label' => 'Revisar estatuto',
                'description' => 'Fluxo completo para propor, aprovar e registrar alteracoes no estatuto social.',
            ],
            [
                'slug' => 'cnpj_cancellation',
                'name' => 'Registro de Baixa de CNPJ',
                'default_title' => 'Baixa de CNPJ',
                'cta_label' => 'Solicitar baixa',
                'description' => 'Reune os dados necessarios para requerer a baixa do CNPJ junto aos orgaos fiscais.',
            ],
            [
                'slug' => 'branch_opening',
                'name' => 'Abertura de Filial',
                'default_title' => 'Processo de abertura de filial',
                'cta_label' => 'Iniciar abertura de filial',
                'description' => 'Controle das informacoes para implantacao de uma nova filial da instituicao.',
            ],
            [
                'slug' => 'trademarks_and_patents_registration',
                'name' => 'Registro de Marcas e Patentes',
                'default_title' => 'Registro de marca ou patente',
                'cta_label' => 'Registrar marca ou patente',
                'description' => 'Organiza os dados e documentos necessarios para registro de marcas e patentes.',
            ],
        ];

        $payload = array_map(function (array $type) use ($now) {
            return array_merge($type, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $types);

        DB::table('process_types')->insert($payload);
    }

    public function down(): void
    {
        Schema::dropIfExists('process_types');
    }
};
