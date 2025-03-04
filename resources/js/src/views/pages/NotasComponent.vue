<template>
    <div>
        <vue-editor placeholder="Comentarios..." :id="get_editor_id" v-model="nota" useCustomImageHandler
            @image-added="handleImageAdded" :editorOptions="editorSettings" :editorToolbar="customToolbar"></vue-editor>
    </div>
</template>
<script>
import { VueEditor, Quill } from "vue2-editor";
import { ImageDrop } from "quill-image-drop-module";
import ImageResize from "quill-image-resize-vue";
import notas from "@services/notas";
Quill.register("modules/imageDrop", ImageDrop);
Quill.register("modules/imageResize", ImageResize);
export default {
    components: {
        VueEditor,
        Quill,
    },
    props: {
        id: {
            type: String,
            required: false,
            default: "editor",
        },
        value: {
            required: false,
            default: "",
        },
    },
    watch: {
        get_editor_id: {
            get() {
                return this.id;
            },
            set(newValue) {
                return newValue;
            },
        },
        nota(newValue) {
            this.$emit('input', newValue)
        },
        value(newValue) {
            this.nota = newValue
        }
    },
    data() {
        return {
            nota: this.value,
            customToolbar: [
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                ["bold", "italic", "underline", "strike"], // toggled buttons
                [
                    { align: "" },
                    { align: "center" },
                    { align: "right" },
                    { align: "justify" },
                ],
                //["blockquote", "code-block"],
                [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
                [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
                [{ color: [] }, { background: [] }], // dropdown with defaults from theme
                ["link", "image", "video"],
                ["clean"], // remove formatting button
            ],
            editorSettings: {
                modules: {
                    imageDrop: true,
                    imageResize: {},
                },
            }
        };
    },
    computed: {
        computedValue: {
            set(value) {
                this.nota = value
            },
            get() {
                return this.nota
            }
        }
    },
    methods: {
        handleImageAdded: function (file, Editor, cursorLocation, resetUploader) {
            var formData = new FormData();
            formData.append("image", file);
            notas
                .upload(formData)
                .then((result) => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch((err) => {
                    console.log(err);
                });
        },
    },
    mounted() {

    },
};
</script>
