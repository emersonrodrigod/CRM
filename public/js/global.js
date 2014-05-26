/**
 * Funcao que busca o endereco de acordo com o CEP.
 * Precisa de conexao com a internet para funcionar.
 */
function getEndereco(obj)
{
    if (obj.getAttribute("maxlength") == obj.value.length)
    {

        if ($.trim($(obj).val()) !== "") {

            $('input[name=cep]').parents('fieldset').find('input, select').attr('disabled', 'disabled');
            $('input[name=endereco]').val('Localizando endereço...');

            $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep=" + $("input[name=cep]").val(), function() {

                if (resultadoCEP["resultado"] == 1) {
                    // troca o valor dos elementos
                    $("input[name=endereco]").val(unescape(resultadoCEP["tipo_logradouro"]) + " " + unescape(resultadoCEP["logradouro"]));
                    $("input[name=bairro]").val(unescape(resultadoCEP["bairro"]));
                    $("select[name=cidade]").val(unescape(resultadoCEP["cidade"]));
                    $("select[name=uf]").val(unescape(resultadoCEP["uf"]));

                    $('input[name=numero]').focus();

                    new dgCidadesEstados({
                        cidade: document.getElementById('cidade'),
                        estado: document.getElementById('uf'),
                        estadoVal: unescape(resultadoCEP["uf"]),
                        cidadeVal: unescape(resultadoCEP["cidade"]),
                        change: true
                    });

                    $('input[name=cep]').parents('fieldset').find('input').removeAttr('disabled');

                } else {
                    $('input[name=cep]').parents('fieldset').find('input').removeAttr('disabled');

                    $('input[name=endereco]').val('');

                    new dgCidadesEstados({
                        cidade: document.getElementById('cidade'),
                        estado: document.getElementById('uf'),
                        change: true
                    });

                    alert('CEP não encontrado. Por favor, digite o endereço manualmente.');

                }

            });
        }

    }
}


function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
;

function isTouchDevice()
{
    var ua = navigator.userAgent;
    var isTouchDevice = (
            ua.match(/iPad/i) ||
            ua.match(/iPhone/i) ||
            ua.match(/iPod/i) ||
            ua.match(/Android/i)
            );

    return isTouchDevice;
}

function growlUI(title, content) {
    $.blockUI({
        message: '<h4>' + title + '</h4><p>' + content + '</p>',
        fadeIn: 700,
        fadeOut: 700,
        timeout: 3000,
        showOverlay: false,
        centerY: false,
        blockMsgClass: 'growlUI',
        css: {
            top: '85px',
            left: '',
            width: '300px',
            right: '10px',
            border: 'none',
            padding: '5px 15px',
            backgroundColor: '#000',
            opacity: 0.75,
            color: '#fff',
            textAlign: 'left'
        }
    });
}
;

$(function() {

    /**
     * FastClick para mobile browsers
     */
    FastClick.attach(document.body);

    //$(".session-content textarea").niceScroll();

    /**
     * Price Format
     */
    $('input.price').priceFormat({
        prefix: '',
        centsSeparator: ',',
        thousandsSeparator: '.',
        limit: false,
        centsLimit: 2
    });

    /**
     * Input Masks
     */
    $('input[name=cep]').inputmask("99999999", {"placeholder": ""});
    $('input[name=cpf]').inputmask("99999999999", {"placeholder": ""});
    $('.cnpj').inputmask("99.999.999/9999-99", {"placeholder": ""});
    $('.tel').inputmask({mask: "(99) 9999-9999 "});
    $('.datepicker').inputmask({mask: "99/99/9999"});
    $('.hora').inputmask({mask: "99:99"});

    /**
     * Datepicker
     */
    $(".datepicker").datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        viewMode: 2
    })
            .on('focus', function() {
                if (isTouchDevice()) {
                    $(this).blur();
                }
            })
            .on('change', function() {
                $('.datepicker').datepicker('hide');
            });

    $('*[data-toggle=tooltip]').tooltip({
        trigger: 'focus',
        placement: 'bottom'
    });

});

$(function() {
    $('.delete').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var answer = confirm("Confirma a exclusão deste registro?");
        if (answer) {
            window.location = url;
        } else {
            return false;
        }
    });
});
