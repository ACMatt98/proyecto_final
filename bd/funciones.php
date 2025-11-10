<?php
class FormatoFechas {
    /*=============================================
    FUNCIÓN PARA CAMBIAR FORMATO DE FECHA
    =============================================*/
    static public function cambiaFormatoFecha($fechaOriginal) {
        if (empty($fechaOriginal) || $fechaOriginal == '0000-00-00' || $fechaOriginal == null) {
            return '';
        }
        
        $partesStamp = explode(" ", $fechaOriginal);
        $partesFecha = explode("-", $partesStamp[0]);
        
        if (count($partesFecha) == 3) {
            return $partesFecha[2] . "/" . $partesFecha[1] . "/" . $partesFecha[0];
        }
        
        return $fechaOriginal; // Si no se puede formatear, devolver original
    }

    /*=============================================
    FUNCIÓN PARA CAMBIAR FORMATO DE HORA
    =============================================*/
    static public function cambiaFormatoHora($horaOriginal) {
        if (($horaOriginal == "") or ($horaOriginal == "0000-00-00")) {
            return "";
        } else {
            return date("H:i", strtotime($horaOriginal));
        }
    }
}
?>