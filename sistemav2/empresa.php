<?php
require_once 'includes/auth.php';
requireLogin();
$currentPage = 'empresa';
$mainHeader = '<h1>Empresa</h1>';
ob_start();
?>
<div class="container mt-4">
    <h2>Cadastro da Empresa</h2>
    <ul class="nav nav-tabs" id="empresaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab">Dados da Empresa</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="config-tab" data-bs-toggle="tab" data-bs-target="#config" type="button" role="tab">Configurações</button>
        </li>
    </ul>
    <form id="empresaForm" enctype="multipart/form-data" class="mt-3">
        <div class="tab-content" id="empresaTabsContent">
            <div class="tab-pane fade show active" id="dados" role="tabpanel">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Empresa</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="col-md-2">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone">
                    </div>
                    <div class="col-md-3">
                        <label for="ie" class="form-label">Inscrição Estadual</label>
                        <input type="text" class="form-control" id="ie" name="ie">
                    </div>
                    <div class="col-md-3">
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cnpj" name="cnpj">
                            <button type="button" class="btn btn-outline-secondary" id="buscar-cnpj" style="width:100px !important;">Buscar</button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco">
                    </div>
                    <div class="col-md-2">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro">
                    </div>
                    <div class="col-md-3">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade">
                    </div>
                    <div class="col-md-1">
                        <label for="estado" class="form-label">UF</label>
                        <input type="text" class="form-control" id="estado" name="estado" maxlength="2" style="max-width:60px;">
                    </div>
                    <div class="col-md-2">
                        <label for="cep" class="form-label">CEP</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cep" name="cep" maxlength="9">                            
                            <button type="button" class="btn btn-outline-secondary" id="buscar-cep" style="width:100px !important;">Buscar</button>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="logo" class="form-label">Logo da Empresa</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                    <div id="logoPreview"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="config" role="tabpanel">
                <div class="mb-3">
                    <label for="fator_custo" class="form-label">Fator de Custo (%)</label>
                    <input type="number" step="0.01" class="form-control" id="fator_custo" name="fator_custo" required>
                    <div class="form-text">Multiplicador aplicado ao custo da tela.</div>
                </div>
                <div class="mb-3">
                    <label for="fator_mao_obra" class="form-label">Fator de Mão de Obra (%)</label>
                    <input type="number" step="0.01" class="form-control" id="fator_mao_obra" name="fator_mao_obra" required>
                    <div class="form-text">Multiplicador aplicado ao valor da mão de obra.</div>
                </div>
                <div class="mb-3">
                    <label for="valor_adicional" class="form-label">Valor Adicional (R$)</label>
                    <input type="number" step="0.01" class="form-control" id="valor_adicional" name="valor_adicional" required>
                    <div class="form-text">Valor fixo adicionado ao total calculado.</div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/views/layout.php';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(function() {
    function carregarEmpresa() {
        $.get('api/api.php?action=empresa', function(resp) {
            if (resp.success && resp.data) {
                $('#nome').val(resp.data.nome);
                $('#cnpj').val(resp.data.cnpj);
                $('#endereco').val(resp.data.endereco);
                $('#telefone').val(resp.data.telefone);
                $('#email').val(resp.data.email);
                $('#fator_custo').val(resp.data.fator_custo);
                $('#fator_mao_obra').val(resp.data.fator_mao_obra);
                $('#valor_adicional').val(resp.data.valor_adicional);
                $('#ie').val(resp.data.ie);
                $('#bairro').val(resp.data.bairro);
                $('#cidade').val(resp.data.cidade);
                $('#estado').val(resp.data.estado);
                $('#cep').val(resp.data.cep);
                if (resp.data.logo) {
                    $('#logoPreview').html('<img src="data:image/png;base64,'+resp.data.logo+'" class="logo-preview" />');
                } else {
                    $('#logoPreview').html('');
                }
            }
        }, 'json');
    }
    carregarEmpresa();

    // Máscara para o campo CEP
    $('#cep').mask('00000-000');
    // Máscara para o campo TELEFONE (dinâmica para fixo e celular)
    var SPMaskBehavior = function (val) {
      return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    };
    $('#telefone').mask(SPMaskBehavior, {
      onKeyPress: function(val, e, field, options) {
        field.mask(SPMaskBehavior.apply({}, arguments), options);
      }
    });

    // Preview da logo ao selecionar
    $('#logo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#logoPreview').html('<img src="'+ev.target.result+'" class="logo-preview" />');
            };
            reader.readAsDataURL(file);
        }
    });

    // Envio do formulário
    $('#empresaForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'api/api.php?action=empresa',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                console.log(resp); // Depuração: mostrar resposta completa no console
                if (resp.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Dados salvos com sucesso!'
                    });
                    carregarEmpresa();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: resp.error || 'Erro ao salvar.'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: (xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Erro desconhecido.')
                });
            }
        });
    });

    // Busca CEP
    $('#buscar-cep').on('click', function() {
        var cep = $('#cep').val().replace(/\D/g, '');
        if (cep.length === 8) {
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        $('#endereco').val(data.logradouro);
                        $('#bairro').val(data.bairro);
                        $('#cidade').val(data.localidade);
                        $('#estado').val(data.uf);
                    } else {
                        Swal.fire('CEP não encontrado!', '', 'warning');
                    }
                })
                .catch(() => Swal.fire('Erro ao buscar o CEP.', '', 'error'));
        } else {
            Swal.fire('CEP inválido!', '', 'warning');
        }
    });
    // Busca automática ao sair do campo CEP
    $('#cep').on('blur', function() {
        $('#buscar-cep').click();
    });

    // Busca CNPJ
    $('#buscar-cnpj').on('click', function() {
        var cnpj = $('#cnpj').val().replace(/\D/g, '');
        if (cnpj.length === 14) {
            $.get('api/api.php?action=buscar_cnpj&cnpj=' + cnpj, function(resp) {
                if (resp.success && resp.data) {
                    $('#nome').val(resp.data.nome);
                    $('#cep').val(resp.data.cep);
                    $('#endereco').val(resp.data.logradouro);
                    $('#bairro').val(resp.data.bairro);
                    $('#cidade').val(resp.data.municipio);
                    $('#estado').val(resp.data.uf);
                } else {
                    Swal.fire('CNPJ não encontrado!', '', 'warning');
                }
            }, 'json').fail(function() {
                Swal.fire('Erro ao buscar o CNPJ.', '', 'error');
            });
        } else {
            Swal.fire('CNPJ inválido!', '', 'warning');
        }
    });
});
</script>
<style>
.logo-preview { max-width: 200px; max-height: 120px; margin-bottom: 10px; }
</style>