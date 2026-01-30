<?php
include "connections/config.php";

if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
} else {
    $fecha = date("Y-m-d");
}
$hora = date("H:i:s.u");

if (isset($_GET['u_id'])) {
    $u_id = $_GET['u_id'];
} else {
    $u_id = $_SESSION['u_id'];
}


?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script>
    var currentTab = 1;
    var inicioOrden1 = 0;
    var inicioOrden2 = 0;
    var activo1 = 0;
    var activo2 = 0;
    var folio = 0;
    var folio1 = 0;
    var folio2 = 0;

    var agoz1 = {};
    var auoz1 = {};
    var agcabeza1 = {};
    var agcola1 = {};
    var aucabeza1 = {};
    var aucola1 = {};
    var agacum1 = {};
    var auacum1 = {};

    var agoz2 = {};
    var auoz2 = {};
    var agcabeza2 = {};
    var agcola2 = {};
    var aucabeza2 = {};
    var aucola2 = {};
    var agacum2 = {};
    var auacum2 = {};
    



    $(document).ready(function() {
        $("#tabla_circuito1").find("input,button,textarea,select").attr("disabled", "disabled");
        $("#tabla_circuito2").find("input,button,textarea,select").attr("disabled", "disabled");
        $('#finalizarOrdenBtn1').prop('disabled', true);
        $('#finalizarOrdenBtn2').prop('disabled', true);

        cargarDatos();



        $('.nav-link').on('click', function(e) {
            var current = $(this);
            currentTab = current.context.dataset.id;
            updateGrafica();
        });


        $('#tabla_circuito1 tbody').on("keydown", "tr:last td:nth-last-child(9) input", function(e) {

            if (e.keyCode === 9 || e.which === 9) {
                e.preventDefault();
                añadirFila();
                $('#tabla_circuito1 tr:last td:nth-child(2) input').focus();
            }
        })

        $('#tabla_circuito1 tbody').on("change", "input", function(e) {
            var row = $(this).closest("tr");
            cambioRow1(row);

        })

        $('#tabla_circuito2 tbody').on("change", "input", function(e) {
            var row = $(this).closest("tr");
            cambioRow2(row);
        })



        $('#tabla_circuito2 tbody').on("keydown", "tr:last td:nth-last-child(9) input", function(e) {

            if (e.keyCode === 9 || e.which === 9) {
                e.preventDefault();
                añadirFila();
                $('#tabla_circuito2 tr:last td:nth-child(2) input').focus();
            }
        })


    });

    function cambioRow1(row) {

        var hora = row.find("select:eq(0)").val();

        var m3hr = row.find("input:eq(0)").val();
        var auppmcabeza = row.find("input:eq(19)").val();
        var auppmcola = row.find("input:eq(20)").val();
        var agppmcabeza = row.find("input:eq(21)").val();
        var agppmcola = row.find("input:eq(22)").val();

        var recup_auoz = row.find("input:eq(23)");
        var recup_auacum = row.find("input:eq(24)");
        var recup_agoz = row.find("input:eq(25)");
        var recup_agacum = row.find("input:eq(26)");
        var recup_agau = row.find("input:eq(27)");
        var recup_au = row.find("input:eq(28)");
        var recup_ag = row.find("input:eq(29)");

        recup_auoz.val('' + ((auppmcabeza - auppmcola) * m3hr) / 31.1035);
        /*$('[id*=recup_auoz1]').each(function(el) {
            sum += parseFloat($(this).val());
            console.log(sum);
            $(this).closest("tr");
            var recup_auacum = row.find("input:eq(25)");
            recup_auacum.val(sum);
        })*/
        recup_agoz.val('' + ((agppmcabeza - agppmcola) * m3hr) / 31.1035);
        recup_agau.val('' + (recup_agoz.val() / recup_auoz.val()));
        recup_au.val('' + (auppmcabeza - auppmcola) / auppmcabeza);
        recup_ag.val('' + (agppmcabeza - agppmcola) / agppmcabeza);

        var myRow = $("#tabla_circuito1 tr").index(row) - 2;

        auoz1[myRow] = recup_auoz.val();
        agoz1[myRow] = recup_agoz.val();
        agcabeza1[hora] = agppmcabeza;
        agcola1[hora] = agppmcola;
        aucabeza1[hora] = auppmcabeza;
        aucola1[hora] = auppmcola;


        updateAcumAu1(auoz1);
        updateAcumAg1(agoz1);

        agacum1[hora] = recup_agacum.val();
        auacum1[hora] = recup_auacum.val();

        const agcabezaordered1 = Object.keys(agcabeza1).sort().reduce(
            (obj, key) => {
                obj[key] = agcabeza1[key];
                return obj;
            }, {}
        );
        const agcolaordered1 = Object.keys(agcola1).sort().reduce(
            (obj, key) => {
                obj[key] = agcola1[key];
                return obj;
            }, {}
        );
        const aucabezaordered1 = Object.keys(aucabeza1).sort().reduce(
            (obj, key) => {
                obj[key] = aucabeza1[key];
                return obj;
            }, {}
        );
        const aucolaordered1 = Object.keys(aucola1).sort().reduce(
            (obj, key) => {
                obj[key] = aucola1[key];
                return obj;
            }, {}
        );
        const auacumordered1 = Object.keys(auacum1).sort().reduce(
            (obj, key) => {
                obj[key] = auacum1[key];
                return obj;
            }, {}
        );
        const agacumordered1 = Object.keys(agacum1).sort().reduce(
            (obj, key) => {
                obj[key] = agacum1[key];
                return obj;
            }, {}
        );

        myChartSA1.data.labels = Object.keys(agcabezaordered1);
        myChartSA1.data.datasets[0].data = Object.values(agcabezaordered1);
        myChartSA1.data.datasets[1].data = Object.values(agcolaordered1);
        myChartSA1.update();

        myChartSA2.data.labels = Object.keys(aucabezaordered1);
        myChartSA2.data.datasets[0].data = Object.values(aucabezaordered1);
        myChartSA2.data.datasets[1].data = Object.values(aucolaordered1);
        myChartSA2.update();

        myChartSA3.data.labels = Object.keys(auacumordered1);
        myChartSA3.data.datasets[0].data = Object.values(auacumordered1);
        myChartSA3.data.datasets[1].data = Object.values(agacumordered1);
        myChartSA3.update();
    }

    function cambioRow2(row) {

        var hora = row.find("select:eq(0)").val();
        var m3hr = row.find("input:eq(0)").val();
        var auppmcabeza = row.find("input:eq(19)").val();
        var auppmcola = row.find("input:eq(20)").val();
        var agppmcabeza = row.find("input:eq(21)").val();
        var agppmcola = row.find("input:eq(22)").val();

        var recup_auoz = row.find("input:eq(23)");
        var recup_auacum = row.find("input:eq(24)");
        var recup_agoz = row.find("input:eq(25)");
        var recup_agacum = row.find("input:eq(26)");
        var recup_agau = row.find("input:eq(27)");
        var recup_au = row.find("input:eq(28)");
        var recup_ag = row.find("input:eq(29)");

        recup_auoz.val('' + ((auppmcabeza - auppmcola) * m3hr) / 31.1035);
        /*$('[id*=recup_auoz1]').each(function(el) {
            sum += parseFloat($(this).val());
            console.log(sum);
            $(this).closest("tr");
            var recup_auacum = row.find("input:eq(25)");
            recup_auacum.val(sum);
        })*/
        recup_agoz.val('' + ((agppmcabeza - agppmcola) * m3hr) / 31.1035);
        recup_agau.val('' + (recup_agoz.val() / recup_auoz.val()));
        recup_au.val('' + (auppmcabeza - auppmcola) / auppmcabeza);
        recup_ag.val('' + (agppmcabeza - agppmcola) / agppmcabeza);

        var myRow = $("#tabla_circuito2 tr").index(row) - 2;

        auoz2[myRow] = recup_auoz.val();
        agoz2[myRow] = recup_agoz.val();
        agcabeza2[hora] = agppmcabeza;
        agcola2[hora] = agppmcola;
        aucabeza2[hora] = auppmcabeza;
        aucola2[hora] = auppmcola;


        updateAcumAu2(auoz2);
        updateAcumAg2(agoz2);

        agacum2[hora] = recup_agacum.val();
        auacum2[hora] = recup_auacum.val();

        const agcabezaordered2 = Object.keys(agcabeza2).sort().reduce(
            (obj, key) => {
                obj[key] = agcabeza2[key];
                return obj;
            }, {}
        );
        const agcolaordered2 = Object.keys(agcola2).sort().reduce(
            (obj, key) => {
                obj[key] = agcola2[key];
                return obj;
            }, {}
        );
        const aucabezaordered2 = Object.keys(aucabeza2).sort().reduce(
            (obj, key) => {
                obj[key] = aucabeza2[key];
                return obj;
            }, {}
        );
        const aucolaordered2 = Object.keys(aucola2).sort().reduce(
            (obj, key) => {
                obj[key] = aucola2[key];
                return obj;
            }, {}
        );
        const auacumordered2 = Object.keys(auacum2).sort().reduce(
            (obj, key) => {
                obj[key] = auacum2[key];
                return obj;
            }, {}
        );
        const agacumordered2 = Object.keys(agacum2).sort().reduce(
            (obj, key) => {
                obj[key] = agacum2[key];
                return obj;
            }, {}
        );

        myChartSA4.data.labels = Object.keys(agcabezaordered2);
        myChartSA4.data.datasets[0].data = Object.values(agcabezaordered2);
        myChartSA4.data.datasets[1].data = Object.values(agcolaordered2);
        myChartSA4.update();

        myChartSA5.data.labels = Object.keys(aucabezaordered2);
        myChartSA5.data.datasets[0].data = Object.values(aucabezaordered2);
        myChartSA5.data.datasets[1].data = Object.values(aucolaordered2);
        myChartSA5.update();

        myChartSA6.data.labels = Object.keys(auacumordered2);
        myChartSA6.data.datasets[0].data = Object.values(auacumordered2);
        myChartSA6.data.datasets[1].data = Object.values(agacumordered2);
        myChartSA6.update();
    }

    function updateAcumAu1(array) {
        sum = 0;
        Object.keys(array).forEach(key => {
            sum = parseFloat(array[key]) + sum;
            $("#tabla_circuito1 tbody tr:nth-child(" + key + ") input:eq(24)").val(sum);
        });
    }

    function updateAcumAg1(array) {
        sum = 0;
        Object.keys(array).forEach(key => {
            sum = parseFloat(array[key]) + sum;
            $("#tabla_circuito1 tbody tr:nth-child(" + key + ") input:eq(26)").val(sum);
        });
    }

    function updateAcumAu2(array) {
        sum = 0;
        Object.keys(array).forEach(key => {
            sum = parseFloat(array[key]) + sum;
            $("#tabla_circuito2 tbody tr:nth-child(" + key + ") input:eq(24)").val(sum);
        });
    }

    function updateAcumAg2(array) {
        sum = 0;
        Object.keys(array).forEach(key => {
            sum = parseFloat(array[key]) + sum;
            $("#tabla_circuito2 tbody tr:nth-child(" + key + ") input:eq(26)").val(sum);
        });
    }

    function finalizarOrden() {
        event.preventDefault();
        var u_id = <?php echo $u_id ?>;
        var fecha = <?php echo $fecha ?>;
        var unidad_id = <?php echo $_GET['unidad_id'] ?>;
        var actual = currentTab;
        var trn_id_rel = 0;
        if (actual == 1) {
            trn_id_rel = folio1;
        } else {
            trn_id_rel = folio2;
        }

        $.ajax({
                url: 'finalizar_orden_despojo.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    trn_id_rel: trn_id_rel,
                    circuito: actual,
                    u_id: u_id
                },
            })
            .done(function(respuesta) {
                var direccionar = '<? echo "\ formato_despojo.php?unidad_id=" ?>' + unidad_id + '&fecha=' + "'" + fecha + "'" + '&u_id=' + u_id;
                window.location.href = direccionar;
            })
    }

    function iniciarOrden() {
        event.preventDefault();
        var actual = currentTab;
        var fecha = "<?php echo $fecha ?>";
        var u_id = <?php echo $u_id ?>;
       // var unidad_id = <?php echo $_GET['unidad_id'] ?>;
        var unidad_id = document.getElementById('mina_seleccionada').value;
        alert(unidad_id);

        $.ajax({
                url: 'crear_orden_despojo.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    actual: actual,
                    fecha: fecha,
                    u_id: u_id,
                    unidad_id: unidad_id
                },
            })
            .done(function(respuesta) {
                if (respuesta == "Ya tiene una orden iniciada.") {
                    alert(respuesta);
                } else {
                    if (respuesta != "Error, por favor contacte a un administrador.") {

                        if (actual == 1) {
                            folio1 = respuesta;
                            inicioOrden1 = 1;
                            $('#iniciarOrdenBtn1').prop('disabled', true);
                            $('#finalizarOrdenBtn1').prop('disabled', false);
                            $("#tabla_circuito1").find("input,button,textarea,select").attr("disabled", false);
                        } else {
                            folio2 = respuesta;
                            inicioOrden2 = 1;
                            $('#iniciarOrdenBtn2').prop('disabled', true);
                            $('#finalizarOrdenBtn2').prop('disabled', false);
                            $("#tabla_circuito2").find("input,button,textarea,select").attr("disabled", false);
                        }
                    } else {
                        alert(respuesta);
                    }
                }

            })
    }

    function cargarDatos() {
        var u_id = <?php echo $u_id ?>;
        var unidad_id = <?php echo $_GET['unidad_id'] ?>;
        var fecha = <?php echo $fecha ?>;
        $.ajax({
                url: 'cargar_datos_despojo.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    u_id: u_id,
                    unidad_id: unidad_id,
                    fecha: fecha
                },
            })
            .done(function(respuesta) {
                if (respuesta != 0) {
                    if (respuesta["folio1"] != null) {
                        var folio_uno = respuesta["folio1"];
                        folio1 = folio_uno;
                        var circuito1 = respuesta["circuito1"];
                        activo1 = respuesta["activo1"];
                        $('#iniciarOrdenBtn1').prop('disabled', true);
                        $('#finalizarOrdenBtn1').prop('disabled', false);
                        $("#tabla_circuito1").find("input,button,textarea,select").attr("disabled", false);

                        inicioOrden1 = 1;
                        $.ajax({
                                url: 'cargar_circuitos_despojo.php',
                                type: 'POST',
                                dataType: 'html',
                                data: {
                                    folio: folio1,
                                    circuito: circuito1
                                },
                            })
                            .done(function(respuesta) {
                                if (respuesta != "") {
                                    $("#tabla_circuito1 tbody").html(respuesta);
                                    $("#tabla_circuito1 tbody tr").each(function() {
                                        $(this).closest("tr").trigger("change");

                                    });
                                    updateGrafica();
                                    checkActivo1();
                                    //validarUsuario();
                                }
                            })
                    }
                    if (respuesta["folio2"] != null) {
                        var folio_dos = respuesta["folio2"];
                        folio2 = folio_dos;
                        var circuito2 = respuesta["circuito2"];
                        activo2 = respuesta["activo2"];
                        $('#iniciarOrdenBtn2').prop('disabled', true);
                        $('#finalizarOrdenBtn2').prop('disabled', false);
                        $("#tabla_circuito2").find("input,button,textarea,select").att
                        inicioOrden2 = 1;
                        $.ajax({
                                url: 'cargar_circuitos_despojo.php',
                                type: 'POST',
                                dataType: 'html',
                                data: {
                                    folio: folio2,
                                    circuito: circuito2
                                },
                            })
                            .done(function(respuesta) {
                                if (respuesta != "") {
                                    $("#tabla_circuito2 tbody").html(respuesta);
                                    $("#tabla_circuito2 tbody tr").each(function() {
                                        $(this).trigger("change");


                                    });
                                    updateGrafica();
                                    checkActivo2();
                                    //validarUsuario();
                                }

                            })
                    }
                }
            })
        

    }

    function updateGrafica() {
        $('#tabla_circuito1 > tbody  > tr').each(function(index, tr) {
            var row = $(this).closest("tr");
            cambioRow1(row);
        })

        $('#tabla_circuito2 > tbody  > tr').each(function(index, tr) {
            var row = $(this).closest("tr");
            cambioRow2(row);
        })

    }

    function añadirFila() {
        if (currentTab == 1 && inicioOrden1 == 1) {
            $('#tabla_circuito1 tbody tr:last').after(`<tr data-id="0" data-circ="1"><td><select class="form-control" style="width:100%"><?php $start = "00:00";
                                                                                                                                            $end = "23:30";

                                                                                                                                            $tStart = strtotime($start);
                                                                                                                                            $tEnd = strtotime($end);
                                                                                                                                            $tNow = $tStart;

                                                                                                                                            while ($tNow <= $tEnd) {
                                                                                                                                                echo "<option value='" . date("H:i", $tNow) . "'>" . date("H:i", $tNow) . "</option>";
                                                                                                                                                $tNow = strtotime("+30 minutes", $tNow);
                                                                                                                                            } ?></select></td><?php for ($i = 0; $i < 23; $i++) {
                                                                                                                                                                    echo '<td><input type="text" class="form-control" style="width:100%"></td>';
                                                                                                                                                                } ?><td><input type="text" id="recup-auoz" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-auacum" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-agoz" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-agacum" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-agau" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-au" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-ag" readonly="readonly" class="form-control" style="width:100%"></td><td><button class="btn btn-primary" onclick="guardarFila(this)"><i class="fa fa-save"></i></button></td></tr>`);
        } else if (currentTab == 2 && inicioOrden2 == 1) {
            $('#tabla_circuito2 tbody tr:last').after(`<tr data-id="0" data-circ="2"><td><select class="form-control" style="width:100%"><?php $start = "00:00";
                                                                                                                                            $end = "23:30";

                                                                                                                                            $tStart = strtotime($start);
                                                                                                                                            $tEnd = strtotime($end);
                                                                                                                                            $tNow = $tStart;

                                                                                                                                            while ($tNow <= $tEnd) {
                                                                                                                                                echo "<option value='" . date("H:i", $tNow) . "'>" . date("H:i", $tNow) . "</option>";
                                                                                                                                                $tNow = strtotime("+30 minutes", $tNow);
                                                                                                                                            } ?></select></td><?php for ($i = 0; $i < 23; $i++) {
                                                                                                                                                                    echo '<td><input type="text" class="form-control" style="width:100%"></td>';
                                                                                                                                                                } ?><td><input type="text" id="recup-auoz" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-auacum" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-agoz" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-agacum" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-agau" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-au" readonly="readonly" class="form-control" style="width:100%"></td><td><input type="text" id="recup-ag" readonly="readonly" class="form-control" style="width:100%"></td><td><button class="btn btn-primary" onclick="guardarFila(this)"><i class="fa fa-save"></i></button></td></tr>`);
        }

    }

    function checkActivo1() {
        if (activo1 == 1) {
            $('#finalizarOrdenBtn1').prop('disabled', true);
            $('#iniciarOrdenBtn1').prop('disabled', true);
            $("#tabla_circuito1").find("input,button,textarea,select").attr("disabled", "disabled");
            inicioOrden1 = 0;
        }
    }

    function checkActivo2() {
        if (activo2 == 1) {
            $('#finalizarOrdenBtn2').prop('disabled', true);
            $('#iniciarOrdenBtn2').prop('disabled', true);
            $("#tabla_circuito2").find("input,button,textarea,select").attr("disabled", "disabled");
            inicioOrden2 = 0;
        }
    }

    function eliminarFila() {
        if ($('#tabla_circuito1 tbody tr').length != 1) {
            if (currentTab == 1 && inicioOrden1 == 1) {
                $('#tabla_circuito1 tbody tr:last').remove();
            } else if (currentTab == 2 && inicioOrden2 == 1) {
                $('#tabla_circuito2 tbody tr:last').remove();
            }
        }

    }

    function validarUsuario() {
        var u_id = <?php echo $u_id ?>;
        var u_id_get = <?php echo $_SESSION['u_id'] ?>;

        if (u_id != u_id_get) {
            $("#tabla_circuito1").find("input,button,textarea,select").attr("disabled", "disabled");
            $("#tabla_circuito2").find("input,button,textarea,select").attr("disabled", "disabled");
            $('#finalizarOrdenBtn1').prop('disabled', true);
            $('#finalizarOrdenBtn2').prop('disabled', true);
            $('#iniciarOrdenBtn1').prop('disabled', true);
            $('#iniciarOrdenBtn2').prop('disabled', true);
            inicioOrden1 = 0;
            inicioOrden2 = 0;
        }
    }

    function guardarFila(e) {
        event.preventDefault();
        data_raw = [];
        inputs = e.parentElement.parentElement.getElementsByTagName('input');
        hora = e.parentElement.parentElement.getElementsByTagName('select');
        let select_hora = hora[0].value;
        var trn_id = e.parentElement.parentElement.dataset.id;
        var circuito = e.parentElement.parentElement.dataset.circ;
        if (circuito == 1) {
            data_raw.push(folio1);
        } else {
            data_raw.push(folio2);
        }
        data_raw.push(select_hora);
        for (var z = 0; z < inputs.length; z++) {
            data_raw.push(inputs[z].value);
        }
        $.ajax({
                url: 'guardar_registro_despojo.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    data: data_raw,
                    trn_id: trn_id
                },
            })
            .done(function(respuesta) {
                alert(respuesta);
            })
        /*myChartSA1.data.labels = Object.keys(agcabeza);
        myChartSA1.data.datasets[0].data = Object.values(agcabeza);
        myChartSA1.data.datasets[1].data = Object.values(agcola);
        myChartSA1.update();

        myChartSA2.data.labels = Object.keys(aucabeza);
        myChartSA2.data.datasets[0].data = Object.values(aucabeza);
        myChartSA2.data.datasets[1].data = Object.values(aucola);
        myChartSA2.update();*/
    }
</script>
<br>
<br>
<div class="d-flex flex-column">
    <div class="col-md-12 col-lg-12 bg-info text-black text-center">
        <br />
        <h4>Orden de trabajo - Despojos</h4>
    </div>
    <div class="col-md-11 col-lg-11">
        <br>
        <br>
        <div class="col-md-1 col-lg-1">
            <h5>Fecha:</h5>
        </div>
        <div class="col-md-2 col-lg-2">
            <input type="date" name="fecha" class="form-control" id="fecha" value="<?php echo $fecha ?>" />
        </div>
        <div class="col-md-1 col-lg-1">
            <h5>Hora:</h5>
        </div>
        <div class='col-sm-2'>
            <select class="form-control" name="hora_sel" id="hora_sel">
                <?
                $start = "00:00";
                $end = "23:30";

                $tStart = strtotime($start);
                $tEnd = strtotime($end);
                $tNow = $tStart;

                while ($tNow <= $tEnd) {
                    echo "<option value='" . date("H:i", $tNow) . "'>" . date("H:i", $tNow) . "</option>";
                    $tNow = strtotime('+30 minutes', $tNow);
                }
                ?>
            </select>
        </div>
        <div class="col-md-2 col-lg-2">
            <?
            $unidad_id = $_GET['unidad_id'];
            if ($unidad_id == "") {
                $nombretop = "Seleccione Mina";
            } else {
                $nomtop = $unidad_id;
                $result = $mysqli->query("SELECT unidad_id, Nombre FROM arg_empr_unidades WHERE unidad_id = " . $unidad_id) or die(mysqli_error($mysqli));
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $nombretop = $row['Nombre'];
                }
            }
            echo ("<form name=\"Busqueda\" id=\"Busqueda\">");
            echo ("<select name=\"mina_seleccionada\" id=\"mina_seleccionada\" class=\"form-control\" > ");
            echo ("<option value=$nomtop>$nombretop</option>");
            $result = $mysqli->query("SELECT unidad_id, Nombre FROM arg_empr_unidades") or die(mysqli_error($mysqli));
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $nombre = ($row["Nombre"]);
                $nomenclatura = $row["unidad_id"];
                echo ("<option value=$nomenclatura>$nombre</option>");
            }
            echo ("</select>");
            ?>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="form-group">
                <button type="button" class="btn btn-secondary" onclick="añadirFila()"> + FILA </button>
                <button type="button" class="btn btn-danger" onclick="eliminarFila()"> - FILA </button>
            </div>
        </div>
    </div>
</div>
<br>
<br>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="nav nav-pills nav-fill" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-id="1" href="#circuito1" data-toggle="tab" aria-controls="circuito1" aria-selected="true">Circuito 1</a>
            </li>
            <li class="pl-2 nav-item">
                <a class="nav-link" data-id="2" href="#circuito2" data-toggle="tab" aria-controls="circuito2" aria-selected="false">Circuito 2</a>
            </li>
        </ul>

    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div id="circuito1" class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-circuito1-tab">
        <div class="pl-5">
            <button class="btn btn-success btn-lg" id="iniciarOrdenBtn1" name="iniciarOrdenBtn1" onclick="iniciarOrden()">Crear orden</button>
        </div>
        <div class="p-5 table-responsive" style="width:100%">
            <div style="width:max-content">
                <table id="tabla_circuito1" name="tabla_circuito1" class="table table-hover table-striped table-bordered" style="text-align:center; font-size: 1.175em; width:max-content">
                    <thead>
                        <tr>
                            <th colspan="1" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup">FLUJOMETRO</th>
                            <th colspan="2" scope="colgroup">STRIP</th>
                            <th colspan="3" scope="colgroup">CALDERA</th>
                            <th colspan="1" scope="colgroup">A</th>
                            <th colspan="2" scope="colgroup">INTERCAMBIADOR</th>
                            <th colspan="2" scope="colgroup">Rectificador 1</th>
                            <th colspan="2" scope="colgroup">Rectificador 2</th>
                            <th colspan="2" scope="colgroup">Rectificador 3</th>
                            <th colspan="2" scope="colgroup">ppm</th>
                            <th colspan="1" scope="colgroup"></th>
                            <th colspan="4" scope="colgroup">EQUIPO DE ABSORCION</th>
                            <th colspan="7" scope="colgroup">RECUPERACION</th>
                            <th colspan="1" scope="colgroup">Guardar</th>
                        </tr>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th colspan="2" scope="colgroup">PRESIONES</th>
                            <th colspan="1" scope="colgroup">TEMP</th>
                            <th colspan="2" scope="colgroup">TEMPERATURA</th>
                            <th colspan="1" scope="colgroup">CELDA</th>
                            <th colspan="2" scope="colgroup">PRESION</th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="1" scope="colgroup">PH ELUYENTE</th>
                            <th colspan="2" scope="colgroup">Au, ppm</th>
                            <th colspan="2" scope="colgroup">Ag, ppm</th>
                            <th colspan="2" scope="colgroup">Au oz</th>
                            <th colspan="2" scope="colgroup">Ag</th>
                            <th colspan="1" scope="colgroup">Ag/Au</th>
                            <th colspan="2" scope="colgroup">Eficiencia</th>
                            <th colspan="1" scope="colgroup"></th>
                        </tr>
                        <tr>
                            <th scope="col">HR</th>
                            <th scope="col">M3/HR</th>
                            <th scope="col">TOTALIZADOR</th>
                            <th scope="col">ENTRADA</th>
                            <th scope="col">SALIDA</th>
                            <th scope="col"></th>
                            <th scope="col">ENTRADA</th>
                            <th scope="col">SALIDA</th>
                            <th scope="col">TEMP</th>
                            <th scope="col">ENTRADA</th>
                            <th scope="col">SALIDA</th>
                            <th scope="col">Volt</th>
                            <th scope="col">Amp</th>
                            <th scope="col">Volt</th>
                            <th scope="col">Amp</th>
                            <th scope="col">Volt</th>
                            <th scope="col">Amp</th>
                            <th scope="col">SOSA</th>
                            <th scope="col">NaCN</th>
                            <th scope="col"></th>
                            <th scope="col">CABEZA</th>
                            <th scope="col">COLA</th>
                            <th scope="col">CABEZA</th>
                            <th scope="col">COLA</th>
                            <th scope="col">Au oz</th>
                            <th scope="col">Au Acum</th>
                            <th scope="col">Ag oz</th>
                            <th scope="col">Ag Acum</th>
                            <th scope="col"></th>
                            <th scope="col">Au</th>
                            <th scope="col">Ag</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>

                    <div id="circuito1_tbody" style="overflow:auto;">
                        <tbody>
                            <tr data-id="0" data-circ="1">
                                <td><select class="form-control" style="width:100%">
                                        <?
                                        $start = "00:00";
                                        $end = "23:30";

                                        $tStart = strtotime($start);
                                        $tEnd = strtotime($end);
                                        $tNow = $tStart;

                                        while ($tNow <= $tEnd) {
                                            echo "<option value='" . date("H:i", $tNow) . "'>" . date("H:i", $tNow) . "</option>";
                                            $tNow = strtotime('+30 minutes', $tNow);
                                        }
                                        ?></select></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-auoz1" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-auacum1" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-agoz1" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-agacum1" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-agau1" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-au1" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-ag1" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><button class="btn btn-primary" onclick="guardarFila(this)"><i class="fa fa-save"></i></button></td>
                            </tr>
                        </tbody>
                    </div>

                </table>
                <div>
                    <button class="btn btn-danger btn-lg" id="finalizarOrdenBtn1" name="finalizarOrdenBtn1" onclick="finalizarOrden()">Finalizar orden</button>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-around">
            <div class="col-sm-4 col-lg-4  d-flex justify-content-center flex-column">
                <p class="lead mt-2 mx-auto">Extracción de AG</p>
                <div class="chart-wrapper px-1">
                    <canvas id="myChartAGOZ1" width="400" height="400"></canvas>
                </div>
            </div>
            <div class="col-sm-4 col-lg-4 d-flex justify-content-center flex-column">
                <p class="lead mt-2 mx-auto">Extracción de AU</p>
                <div class="chart-wrapper px-1">
                    <canvas id="myChartAUOZ1" width="400" height="400"></canvas>
                </div>
            </div>
            <div class="col-sm-4 col-lg-4 d-flex justify-content-center flex-column">
                <p class="lead mt-2 mx-auto">Onzas Acumm</p>
                <div class="chart-wrapper px-1">
                    <canvas id="myChartACUM1" width="400" height="400"></canvas>
                </div>
            </div>
        </div>

    </div>
    <div id="circuito2" class="tab-pane fade" role="tabpanel" aria-labelledby="nav-circuito2-tab">
        <div class="pl-5">
            <button class="btn btn-success btn-lg" id="iniciarOrdenBtn2" name="iniciarOrdenBtn2" onclick="iniciarOrden()">Crear orden</button>
        </div>
        <div class="p-5 table-responsive" style="width:100%">
            <div style="width:max-content">
                <table id="tabla_circuito2" name="tabla_circuito2" class="table table-hover table-striped table-bordered" style="text-align:center; font-size: 1.175em; width:max-content">
                    <thead>
                        <tr>
                            <th colspan="1" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup">FLUJOMETRO</th>
                            <th colspan="2" scope="colgroup">STRIP</th>
                            <th colspan="3" scope="colgroup">CALDERA</th>
                            <th colspan="1" scope="colgroup">A</th>
                            <th colspan="2" scope="colgroup">INTERCAMBIADOR</th>
                            <th colspan="2" scope="colgroup">Rectificador 4</th>
                            <th colspan="2" scope="colgroup">Rectificador 5</th>
                            <th colspan="2" scope="colgroup">Rectificador 6</th>
                            <th colspan="2" scope="colgroup">ppm</th>
                            <th colspan="1" scope="colgroup"></th>
                            <th colspan="4" scope="colgroup">EQUIPO DE ABSORCION</th>
                            <th colspan="7" scope="colgroup">RECUPERACION</th>
                            <th colspan="1" scope="colgroup">Guardar</th>
                        </tr>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th colspan="2" scope="colgroup">PRESIONES</th>
                            <th colspan="1" scope="colgroup">TEMP</th>
                            <th colspan="2" scope="colgroup">TEMPERATURA</th>
                            <th colspan="1" scope="colgroup">CELDA</th>
                            <th colspan="2" scope="colgroup">PRESION</th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="2" scope="colgroup"></th>
                            <th colspan="1" scope="colgroup">PH ELUYENTE</th>
                            <th colspan="2" scope="colgroup">Au, ppm</th>
                            <th colspan="2" scope="colgroup">Ag, ppm</th>
                            <th colspan="2" scope="colgroup">Au oz</th>
                            <th colspan="2" scope="colgroup">Ag</th>
                            <th colspan="1" scope="colgroup">Ag/Au</th>
                            <th colspan="2" scope="colgroup">Eficiencia</th>
                            <th colspan="1" scope="colgroup"></th>
                        </tr>
                        <tr>
                            <th scope="col">HR</th>
                            <th scope="col">M3/HR</th>
                            <th scope="col">TOTALIZADOR</th>
                            <th scope="col">ENTRADA</th>
                            <th scope="col">SALIDA</th>
                            <th scope="col"></th>
                            <th scope="col">ENTRADA</th>
                            <th scope="col">SALIDA</th>
                            <th scope="col">TEMP</th>
                            <th scope="col">ENTRADA</th>
                            <th scope="col">SALIDA</th>
                            <th scope="col">Volt</th>
                            <th scope="col">Amp</th>
                            <th scope="col">Volt</th>
                            <th scope="col">Amp</th>
                            <th scope="col">Volt</th>
                            <th scope="col">Amp</th>
                            <th scope="col">SOSA</th>
                            <th scope="col">NaCN</th>
                            <th scope="col"></th>
                            <th scope="col">CABEZA</th>
                            <th scope="col">COLA</th>
                            <th scope="col">CABEZA</th>
                            <th scope="col">COLA</th>
                            <th scope="col">Au oz</th>
                            <th scope="col">Au Acum</th>
                            <th scope="col">Ag oz</th>
                            <th scope="col">Ag Acum</th>
                            <th scope="col"></th>
                            <th scope="col">Au</th>
                            <th scope="col">Ag</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>

                    <div id="circuito2_tbody" style="overflow:auto;">
                        <tbody>
                            <tr data-id="0" data-circ="2">
                                <td><select class="form-control" style="width:100%">
                                        <?
                                        $start = "00:00";
                                        $end = "23:30";

                                        $tStart = strtotime($start);
                                        $tEnd = strtotime($end);
                                        $tNow = $tStart;

                                        while ($tNow <= $tEnd) {
                                            echo "<option value='" . date("H:i", $tNow) . "'>" . date("H:i", $tNow) . "</option>";
                                            $tNow = strtotime('+30 minutes', $tNow);
                                        }
                                        ?></select></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-auoz2" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-auacum2" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-agoz2" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-agacum2" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-agau2" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-au2" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><input type="text" id="recup-ag2" readonly="readonly" class="form-control" style="width:100%"></td>
                                <td><button class="btn btn-primary" onclick="guardarFila(this)"><i class="fa fa-save"></i></button></td>
                            </tr>
                        </tbody>
                    </div>

                </table>
                <div>
                    <button class="btn btn-danger btn-lg" id="finalizarOrdenBtn2" name="finalizarOrdenBtn2" onclick="finalizarOrden()">Finalizar orden</button>
                </div>
            </div>

        </div>
        <div class="row d-flex justify-content-around">
            <div class="col-sm-4 col-lg-4  d-flex justify-content-center flex-column">
                <p class="lead mt-2 mx-auto">Extracción de AG</p>
                <div class="chart-wrapper px-1">
                    <canvas id="myChartAGOZ2" width="400" height="400"></canvas>
                </div>
            </div>
            <div class="col-sm-4 col-lg-4 d-flex justify-content-center flex-column">
                <p class="lead mt-2 mx-auto">Extracción de AU</p>
                <div class="chart-wrapper px-1">
                    <canvas id="myChartAUOZ2" width="400" height="400"></canvas>
                </div>
            </div>
            <div class="col-sm-4 col-lg-4 d-flex justify-content-center flex-column">
                <p class="lead mt-2 mx-auto">Onzas Acumm</p>
                <div class="chart-wrapper px-1">
                    <canvas id="myChartACUM2" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctxe = document.getElementById('myChartAGOZ1').getContext('2d');
    const myChartSA1 = new Chart(ctxe, {
        type: 'line',
        data: {
            labels: Object.keys(agcabeza1),
            datasets: [{
                    label: 'CABEZA AG',
                    data: Object.values(agcabeza1),
                    borderColor: 'rgba(68,114,196,1)',
                    backgroundColor: 'rgba(68,114,196,0.2)',
                },
                {
                    label: 'COLA AG',
                    data: Object.values(agcola1),
                    borderColor: 'rgba(237,125,49,1)',
                    backgroundColor: 'rgba(237,125,49,0.2)',
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Extracción de AG',
                    position: 'top'
                }
            }
        }
    });

    const ctxe2 = document.getElementById('myChartAUOZ1').getContext('2d');
    const myChartSA2 = new Chart(ctxe2, {
        type: 'line',
        data: {
            labels: Object.keys(aucabeza1),
            datasets: [{
                    label: 'CABEZA AU',
                    data: Object.values(aucabeza1),
                    borderColor: 'rgba(68,114,196,1)',
                    backgroundColor: 'rgba(68,114,196,0.2)',
                },
                {
                    label: 'COLA AU',
                    data: Object.values(aucola1),
                    borderColor: 'rgba(237,125,49,1)',
                    backgroundColor: 'rgba(237,125,49,0.2)',
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Extracción de AU',
                    position: 'bottom'
                }
            }
        }
    });
    const ctxe3 = document.getElementById('myChartACUM1').getContext('2d');
    const myChartSA3 = new Chart(ctxe3, {
        type: 'line',
        data: {
            labels: Object.keys(aucabeza1),
            datasets: [{
                    label: 'AU ACUM',
                    data: Object.values(aucabeza1),
                    borderColor: 'rgba(68,114,196,1)',
                    backgroundColor: 'rgba(68,114,196,0.2)',
                },
                {
                    label: 'AG ACUM',
                    data: Object.values(aucola1),
                    borderColor: 'rgba(237,125,49,1)',
                    backgroundColor: 'rgba(237,125,49,0.2)',
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Extracción de AU',
                    position: 'bottom'
                }
            }
        }
    });
    const ctxe4 = document.getElementById('myChartAGOZ2').getContext('2d');
    const myChartSA4 = new Chart(ctxe4, {
        type: 'line',
        data: {
            labels: Object.keys(agcabeza2),
            datasets: [{
                    label: 'CABEZA AG',
                    data: Object.values(agcabeza2),
                    borderColor: 'rgba(68,114,196,1)',
                    backgroundColor: 'rgba(68,114,196,0.2)',
                },
                {
                    label: 'COLA AG',
                    data: Object.values(agcola2),
                    borderColor: 'rgba(237,125,49,1)',
                    backgroundColor: 'rgba(237,125,49,0.2)',
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Extracción de AG',
                    position: 'top'
                }
            }
        }
    });

    const ctxe5 = document.getElementById('myChartAUOZ2').getContext('2d');
    const myChartSA5 = new Chart(ctxe5, {
        type: 'line',
        data: {
            labels: Object.keys(aucabeza2),
            datasets: [{
                    label: 'CABEZA AU',
                    data: Object.values(aucabeza2),
                    borderColor: 'rgba(68,114,196,1)',
                    backgroundColor: 'rgba(68,114,196,0.2)',
                },
                {
                    label: 'COLA AU',
                    data: Object.values(aucola2),
                    borderColor: 'rgba(237,125,49,1)',
                    backgroundColor: 'rgba(237,125,49,0.2)',
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Extracción de AU',
                    position: 'bottom'
                }
            }
        }
    });
    const ctxe6 = document.getElementById('myChartACUM2').getContext('2d');
    const myChartSA6 = new Chart(ctxe6, {
        type: 'line',
        data: {
            labels: Object.keys(aucabeza2),
            datasets: [{
                    label: 'AU ACUM',
                    data: Object.values(aucabeza2),
                    borderColor: 'rgba(68,114,196,1)',
                    backgroundColor: 'rgba(68,114,196,0.2)',
                },
                {
                    label: 'AG ACUM',
                    data: Object.values(aucola2),
                    borderColor: 'rgba(237,125,49,1)',
                    backgroundColor: 'rgba(237,125,49,0.2)',
                }
            ]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Extracción de AU',
                    position: 'bottom'
                }
            }
        }
    });
</script>