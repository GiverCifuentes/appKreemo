function soloLetras(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
    especiales = [8];

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if(letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}



$(document).ready(function() {
    $("#FrmEditarEstado").submit(function(event){
        event.preventDefault();
        // alert("llega");
        let validado=0;

        if ($("#Estado_Cotizacion").val() == 0) {

            $("#val_estado_coti").text("*");
            $("#val_estado_coti2").text("Debes ingresar un  estado");
        }else{

            $("#val_estado_coti").text("");
            $("#val_estado_coti2").text("");
            validado++;
        }

        if (validado==1)
        {
            Swal.fire({
                title:'Registro exitoso',text:'Estado de Cotización Editado!!',icon:'success',footer:'<span class="validacion">Kreemo Solution Systems',
                   //width: '50%',
                padding:'1rem',
                   //background:'#000',
                backdrop:true,
                   //toast: true,
                position:'center',
                    });

            document.FrmEditarEstado.submit();
        }
        else{
            Swal.fire({
                title:'Error en la edicion',text:'Campos pendientes por validar',icon:'error',footer:'<span class="validacion">Kreemo Solution Systems',
                   //width: '50%',
                padding:'1rem',
                   //background:'#000',
                backdrop:true,
                   //toast: true,
                position:'center',
                    });

            // alert("Campos pendientes por validar");


            validado = 0;
        }

    });
});
