import { PermisosModulo } from "@/VariablesGlobales";

/**con esta funcion valido si el usuario tiene cierto permiso sobre algun modulo, tomand
 * con parametros la url del modulo y el id del permiso
 * esta funcion esta importada de manera global desde el main.js
 */
export const modulo = {
    permiso: (url_modulo, id_permiso) => {
        if (PermisosModulo != null) {
            for (let index = 0; index < PermisosModulo.length; index++) {
                //buscando la ruta y el permiso para definir si puede hacer uso de alguna funcion en especifica en un formulario
                if (
                    PermisosModulo[index].url == url_modulo &&
                    PermisosModulo[index].permisos_id == id_permiso
                ) {
                    /**el modulo cuenta con el permiso_id que se mando desde la interfaz */
                    return true;
                }
            }

            return false;
        } else {
            //se recarga la pagina para que se actualice el AccessPermission
            location.reload();
        }
    }
};
