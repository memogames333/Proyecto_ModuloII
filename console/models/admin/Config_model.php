<?php
class Config_model extends CI_Model {
    //Retorna la configuracion del sistema
    function get_data(){
        $this->db->where('id_configuracion',1);
        $query = $this->db->get("configuracion");
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return 0;
    }

    function get_data_sitio(){
        $this->db->where('id_sitio',1);
        $query = $this->db->get("sitio_web");
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return 0;
    }
}
?>
