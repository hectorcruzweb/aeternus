import Vue from "vue";
// GLOBAL FILE DOWNLOADER
Vue.prototype.$downloadFileExcel = function (blobData, baseName = "archivo") {
    try {
        const blob =
            blobData instanceof Blob
                ? blobData
                : new Blob([blobData], {
                      type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                  });

        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        const filename = `${baseName} ${this.$fechaHora()}.xlsx`;
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
        this.$vs.notify({
            title: "Descarga Archivo",
            text: "El archivo se ha descargado correctamente.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "success",
            time: 5000,
        });
    } catch (e) {
        this.$error("Download error:", e);
        this.$vs.notify({
            title: "Error",
            text: "Ha ocurrido un error al tratar de descargar el archivo.",
            iconPack: "feather",
            icon: "icon-alert-circle",
            color: "danger",
            position: "bottom-right",
            time: "4000",
        });
    } finally {
    }
};
