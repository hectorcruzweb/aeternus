<template>
    <div>
        <vs-popup
            :class="['forms-popup bg-content-theme', z_index]"
            fullscreen
            close="cancelar"
            :title="
                getTipoformulario == 'servicio_funerario'
                    ? 'Contrato de Servicio Funerario'
                    : 'Servicio de Exhumación'
            "
            :active="localShow"
            :ref="this.$options.name"
        >
            <!--inicio venta-->
            <div>
                <vs-tabs alignment="left" position="top" v-model="activeTab">
                    <vs-tab label="FALLECIDO"></vs-tab>
                    <vs-tab label="CERTIFICADO MÉDICO"></vs-tab>
                    <vs-tab label="DESTINOS DEL SERVICIO"></vs-tab>
                    <vs-tab label="MATERIAL DE VELACIÓN"></vs-tab>
                    <vs-tab label="ACTA DE DEFUNCIÓN"></vs-tab>
                    <vs-tab label="USO DE CONVENIOS "></vs-tab>
                    <vs-tab label="CONTRATO "></vs-tab>
                    <!--<vs-tab label="FACTURACIÓN" icon="fingerprint"></vs-tab>-->
                </vs-tabs>
                <div class="tab-content" v-show="activeTab == 0">
                    <!--contenido del informacion del fallecido-->
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <div class="form-group">
                                <div class="title-form-group">
                                    Información Personal del Fallecido
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full xl:w-4/12 px-2 input-text"
                                        >
                                            <label>
                                                Título de Tratamiento
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="titulos"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.titulo"
                                                class="w-full"
                                                v-validate:titulo_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="titulo"
                                                data-vv-as=" "
                                                ref="titulo_ref"
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{ errors.first("titulo") }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores['titulo.value']
                                                "
                                                >{{
                                                    errores["titulo.value"][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full xl:w-4/12 px-2 input-text"
                                        >
                                            <label>
                                                Nombre del Fallecido
                                                <span>(*)</span>
                                            </label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="nombre_afectado"
                                                data-vv-as=" "
                                                v-validate.disabled="'required'"
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre Fallecido"
                                                v-model="form.nombre_afectado"
                                                ref="fallecido_ref"
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "nombre_afectado"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores.nombre_afectado
                                                "
                                                >{{
                                                    errores.nombre_afectado[0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full xl:w-4/12 px-2 input-text"
                                        >
                                            <label>
                                                Fecha de Nacimiento
                                                <span>(*)</span>
                                            </label>
                                            <flat-pickr
                                                :disabled="esExhumacion"
                                                name="fecha_nacimiento"
                                                data-vv-as=" "
                                                v-validate:fecha_nacimiento_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="configdateTimePicker"
                                                v-model="form.fecha_nacimiento"
                                                placeholder="Fecha de Nacimiento"
                                                class="w-full"
                                            />
                                            <span>{{
                                                errors.first("fecha_nacimiento")
                                            }}</span>
                                            <span
                                                v-if="
                                                    this.errores
                                                        .fecha_nacimiento
                                                "
                                                >{{
                                                    errores.fecha_nacimiento[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full xl:w-6/12 px-2 input-text"
                                        >
                                            <label>
                                                Género
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="generos"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.genero"
                                                class="w-full"
                                                v-validate:genero_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="genero"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{ errors.first("genero") }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores['genero.value']
                                                "
                                                >{{
                                                    errores["genero.value"][0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full xl:w-6/12 px-2 input-text"
                                        >
                                            <label>
                                                Nacionalidad
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="nacionalidades"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.nacionalidad"
                                                class="w-full"
                                                v-validate:nacionalidad_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="nacionalidad"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first("nacionalidad")
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'nacionalidad.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "nacionalidad.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Entidad de Nacimiento</label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="lugar_nacimiento"
                                                maxlength="100"
                                                type="text"
                                                class="w-full"
                                                placeholder="Lugar donde nació el fallecido"
                                                v-model="form.lugar_nacimiento"
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "lugar_nacimiento"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores
                                                        .lugar_nacimiento
                                                "
                                                >{{
                                                    errores.lugar_nacimiento[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Ocupación Habitual</label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="ocupacion"
                                                maxlength="75"
                                                type="text"
                                                class="w-full"
                                                placeholder="Ocupación del fallecido"
                                                v-model="form.ocupacion"
                                            />
                                            <span>
                                                {{ errors.first("ocupacion") }}
                                            </span>
                                            <span v-if="this.errores.ocupacion">
                                                {{ errores.ocupacion[0] }}
                                            </span>
                                        </div>
                                        <div class="w-full input-text px-2">
                                            <label>Último Domicilio</label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="direccion_fallecido"
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Última dirección del fallecido"
                                                v-model="
                                                    form.direccion_fallecido
                                                "
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "direccion_fallecido"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores
                                                        .direccion_fallecido
                                                "
                                                >{{
                                                    errores
                                                        .direccion_fallecido[0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Estado Civil
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="estados_civiles"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.estado_civil"
                                                class="w-full"
                                                v-validate:estado_civil_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="estado_civil"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first("estado_civil")
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'estado_civil.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "estado_civil.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Escolaridad
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="escolaridades"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.escolaridad"
                                                class="w-full"
                                                v-validate:escolaridad_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="escolaridad"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first("escolaridad")
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'escolaridad.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "escolaridad.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Afiliado a
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="afiliaciones"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.afiliacion"
                                                class="w-full"
                                                v-validate:afiliacion_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="afiliacion"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{ errors.first("afiliacion") }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'afiliacion.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "afiliacion.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Indique la afiliación</label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="afiliacion_nota"
                                                maxlength="75"
                                                type="text"
                                                class="w-full"
                                                placeholder="Describa esa otra afiliación"
                                                v-model="form.afiliacion_nota"
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "afiliacion_nota"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores.afiliacion_nota
                                                "
                                                >{{
                                                    errores.afiliacion_nota[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--contenido del informacion del fallecido-->
                </div>
                <div class="tab-content" v-show="activeTab == 1">
                    <!--contenido del certificado medico-->
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <div class="form-group">
                                <div class="title-form-group">
                                    Información Para el Certificado de Defunción
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label
                                                >Folio del Certificado
                                                Médico</label
                                            >
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="folio_certificado"
                                                maxlength="45"
                                                type="text"
                                                class="w-full"
                                                placeholder="Número de Folio"
                                                v-model="form.folio_certificado"
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "folio_certificado"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .folio_certificado
                                                "
                                                >{{
                                                    errores.folio_certificado[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Fecha y Hora del Fallecimiento
                                                <span>(*)</span>
                                            </label>
                                            <flat-pickr
                                                :disabled="esExhumacion"
                                                name="fechahora_defuncion"
                                                data-vv-as=" "
                                                v-validate:fechahora_defuncion_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="
                                                    configdateTimePickerWithTime
                                                "
                                                v-model="
                                                    form.fechahora_defuncion
                                                "
                                                placeholder="Fecha y hora del fallecimiento"
                                                class="w-full"
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "fechahora_defuncion"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .fechahora_defuncion
                                                "
                                                >{{
                                                    errores
                                                        .fechahora_defuncion[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Causa de Muerte
                                                <span>(*)</span>
                                            </label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="causa_muerte"
                                                data-vv-as=" "
                                                v-validate.disabled="'required'"
                                                maxlength="100"
                                                type="text"
                                                class="w-full"
                                                placeholder="Cáusa de la muerte"
                                                v-model="form.causa_muerte"
                                            />

                                            <span>
                                                {{
                                                    errors.first("causa_muerte")
                                                }}
                                            </span>

                                            <span
                                                v-if="this.errores.causa_muerte"
                                                >{{
                                                    errores.causa_muerte[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2"
                                        >
                                            <label>
                                                Muerte Natural
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="sino"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.muerte_natural_b"
                                                class="w-full"
                                                name="muerte_natural_b"
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>

                                            <span>
                                                {{
                                                    errors.first(
                                                        "muerte_natural_b"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores[
                                                        'muerte_natural_b.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "muerte_natural_b.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2"
                                        >
                                            <label>
                                                Enfermedad Contagiosa
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="sino"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.contagioso_b"
                                                class="w-full"
                                                name="contagioso_b"
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>

                                            <span>
                                                {{
                                                    errors.first("contagioso_b")
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores[
                                                        'contagioso_b.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "contagioso_b.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Lugar de Fallecimiento
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="sitios_muerte"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.sitio_muerte"
                                                class="w-full"
                                                v-validate:sitio_muerte_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="sitio_muerte"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>

                                            <span>
                                                {{
                                                    errors.first("sitio_muerte")
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores[
                                                        'sitio_muerte.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "sitio_muerte.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Indique dirección</label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="lugar_muerte"
                                                maxlength="125"
                                                type="text"
                                                class="w-full"
                                                placeholder="Dirección donde murió"
                                                v-model="form.lugar_muerte"
                                            />

                                            <span>
                                                {{
                                                    errors.first("lugar_muerte")
                                                }}
                                            </span>

                                            <span
                                                v-if="this.errores.lugar_muerte"
                                                >{{
                                                    errores.lugar_muerte[0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                ¿Atención Médica Antes de morir?
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :disabled="esExhumacion"
                                                :options="sino"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.atencion_medica_b"
                                                class="w-full"
                                                name="atencion_medica_b"
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>

                                            <span>
                                                {{
                                                    errors.first(
                                                        "atencion_medica_b"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores[
                                                        'atencion_medica_b.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "atencion_medica_b.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label
                                                >Enfermedades que Padecía</label
                                            >
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="enfermedades_padecidas"
                                                maxlength="125"
                                                type="text"
                                                class="w-full"
                                                placeholder="Enfermedades que padecía"
                                                v-model="
                                                    form.enfermedades_padecidas
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "enfermedades_padecidas"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .enfermedades_padecidas
                                                "
                                                >{{
                                                    errores
                                                        .enfermedades_padecidas[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-4/12 px-2"
                                        >
                                            <label>Nombre del Informante</label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="certificado_informante"
                                                maxlength="125"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre del informante para el certificado"
                                                v-model="
                                                    form.certificado_informante
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "certificado_informante"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .certificado_informante
                                                "
                                                >{{
                                                    errores
                                                        .certificado_informante[0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-4/12 px-2"
                                        >
                                            <label
                                                >Teléfono del Informante</label
                                            >
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="certificado_informante_telefono"
                                                maxlength="45"
                                                type="text"
                                                class="w-full"
                                                placeholder="Teléfono del informante"
                                                v-model="
                                                    form.certificado_informante_telefono
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "certificado_informante_telefono"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .certificado_informante_telefono
                                                "
                                                >{{
                                                    errores
                                                        .certificado_informante_telefono[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-4/12 px-2"
                                        >
                                            <label
                                                >Parentesco con el
                                                fallecido</label
                                            >
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="certificado_informante_parentesco"
                                                maxlength="65"
                                                type="text"
                                                class="w-full"
                                                placeholder="Parentesco con el Fallecido"
                                                v-model="
                                                    form.certificado_informante_parentesco
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "certificado_informante_parentesco"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .certificado_informante_parentesco
                                                "
                                                >{{
                                                    errores
                                                        .certificado_informante_parentesco[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            :class="[
                                                'w-full input-text px-2',
                                                form.legista.value != 1
                                                    ? 'xl:w-6/12'
                                                    : 'xl:w-4/12',
                                            ]"
                                        >
                                            <label>
                                                Médico Legista
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :options="legistas"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.legista"
                                                class="w-full"
                                                v-validate:legista_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="legista"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{ errors.first("legista") }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'legista.value'
                                                    ]
                                                "
                                                >{{
                                                    errores["legista.value"][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            v-show="form.legista.value == 1"
                                            class="w-full input-text xl:w-4/12 px-2"
                                        >
                                            <label
                                                >Nombre del Médico
                                                Legista</label
                                            >
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="medico_legista"
                                                maxlength="125"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre del médico legista"
                                                v-model="form.medico_legista"
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "medico_legista"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores.medico_legista
                                                "
                                                >{{
                                                    errores.medico_legista[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            v-show="form.legista.value == 1"
                                            class="w-full input-text xl:w-4/12 px-2"
                                        >
                                            <label>Cédula Médico Legista</label>
                                            <vs-input
                                                :disabled="esExhumacion"
                                                name="cedula_legista"
                                                maxlength="125"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre del médico legista"
                                                v-model="form.cedula_legista"
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "cedula_legista"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores.cedula_legista
                                                "
                                                >{{
                                                    errores.cedula_legista[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            :class="[
                                                'w-full input-text px-2',
                                                form.legista.value == 1
                                                    ? 'xl:w-12/12'
                                                    : 'xl:w-6/12',
                                            ]"
                                        >
                                            <label>
                                                Estado del Cuerpo
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :options="estados_cuerpo"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.estado_cuerpo"
                                                class="w-full"
                                                v-validate:estado_cuerpo_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="estado_cuerpo"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first(
                                                        "estado_cuerpo"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'estado_cuerpo.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "estado_cuerpo.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--contenido del certificado medico-->
                </div>

                <div class="tab-content" v-show="activeTab == 2">
                    <!--contenido de los destinos del servicio-->
                    <div class="flex flex-wrap">
                        <div
                            class="w-full xl:w-6/12 px-2 h-full py-4"
                            v-show="!esExhumacion"
                        >
                            <div class="form-group py-6">
                                <div class="title-form-group">
                                    Embalsamiento
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-4/12 px-2 text-center"
                                        >
                                            <label>¿Embalsamar Cuerpo?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    :disabled="esExhumacion"
                                                    vs-name="embalsamar_b"
                                                    v-model="form.embalsamar_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    :disabled="esExhumacion"
                                                    vs-name="embalsamar_b"
                                                    v-model="form.embalsamar_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="w-full input-text px-2 xl:w-8/12"
                                        >
                                            <label>
                                                Lista de Preparadores
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :options="embsalsamadores"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.embalsamador"
                                                class="w-full"
                                                v-validate:embalsamador_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="embalsamador"
                                                data-vv-as=" "
                                                :disabled="
                                                    form.embalsamar_b != 1
                                                        ? true
                                                        : false
                                                "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first("embalsamador")
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'embalsamador.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "embalsamador.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <!---
                                        <div class="w-full xl:w-8/12 px-2 input-text">
                                            <label>Nombre del Médico Responsable</label>
                                            <vs-input name="medico_responsable_embalsamado" maxlength="150" type="text"
                                                class="w-full" placeholder="Médico Responsable"
                                                v-model="form.medico_responsable_embalsamado" :disabled="(form.embalsamar_b != 1 ? true : false) ||
                                                    esExhumacion
                                                    " />
                                            <span>
                                                {{ errors.first("medico_responsable_embalsamado") }}
                                            </span>
                                            <span v-if="this.errores.medico_responsable_embalsamado">{{
                                                errores.medico_responsable_embalsamado[0] }}</span>
                                        </div>
                                    -->
                                        <div
                                            class="'w-full px-2 xl:w-6/12 input-text"
                                        >
                                            <label>
                                                Nombre del preparador
                                                <span
                                                    v-if="
                                                        form.embalsamar_b == 1
                                                    "
                                                    >(*)</span
                                                >
                                            </label>
                                            <vs-input
                                                v-if="
                                                    form.embalsamar_b == 1 &&
                                                    form.embalsamador.value == 2
                                                "
                                                name="preparador"
                                                data-vv-as=" "
                                                v-validate:preparador_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre del preparador"
                                                v-model="form.preparador"
                                            />
                                            <vs-input
                                                v-else
                                                type="text"
                                                class="w-full"
                                                value="N/A"
                                                disabled="true"
                                            />
                                            <span>
                                                {{ errors.first("preparador") }}
                                            </span>
                                            <span
                                                v-if="this.errores.preparador"
                                                >{{
                                                    errores.preparador[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="'w-full px-2 xl:w-6/12 input-text"
                                        >
                                            <label>
                                                Cédula del preparador
                                                <span
                                                    v-if="
                                                        form.embalsamar_b == 1
                                                    "
                                                    >(*)</span
                                                >
                                            </label>
                                            <vs-input
                                                v-if="
                                                    form.embalsamar_b == 1 &&
                                                    form.embalsamador.value == 2
                                                "
                                                name="cedula_embalsamador"
                                                data-vv-as=" "
                                                v-validate:preparador_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Cédula del preparador"
                                                v-model="
                                                    form.cedula_embalsamador
                                                "
                                            />
                                            <vs-input
                                                v-else
                                                type="text"
                                                class="w-full"
                                                value="N/A"
                                                disabled="true"
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "cedula_embalsamador"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .cedula_embalsamador
                                                "
                                                >{{
                                                    errores
                                                        .cedula_embalsamador[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="w-full xl:w-6/12 px-2 h-full py-4"
                            v-show="!esExhumacion"
                        >
                            <div class="form-group py-6">
                                <div class="title-form-group">Velación</div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full xl:w-3/12 px-2 text-center input-text"
                                        >
                                            <label>¿Velar Cuerpo?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    :disabled="esExhumacion"
                                                    vs-name="velacion_b"
                                                    v-model="form.velacion_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    :disabled="esExhumacion"
                                                    vs-name="velacion_b"
                                                    v-model="form.velacion_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>

                                        <div
                                            class="w-full xl:w-9/12 px-2 input-text"
                                        >
                                            <label>
                                                ¿Lugar de Velación?
                                                <span
                                                    v-if="form.velacion_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <v-select
                                                :options="lugares_servicio"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.lugar_servicio"
                                                class="w-full"
                                                v-validate:lugar_servicio_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="lugar_servicio"
                                                data-vv-as=" "
                                                :disabled="
                                                    (form.velacion_b != 1
                                                        ? true
                                                        : false) || esExhumacion
                                                "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>

                                            <span>
                                                {{
                                                    errors.first(
                                                        "lugar_servicio"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores[
                                                        'lugar_servicio.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "lugar_servicio.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div class="w-full px-2 input-text">
                                            <label>
                                                Dirección del Servicio
                                                <span
                                                    v-if="form.velacion_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <vs-input
                                                name="direccion_velacion"
                                                data-vv-as=" "
                                                v-validate:direccion_velacion_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Dirección donde se velará"
                                                v-model="
                                                    form.direccion_velacion
                                                "
                                                :disabled="
                                                    (form.velacion_b != 1
                                                        ? true
                                                        : false) || esExhumacion
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "direccion_velacion"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .direccion_velacion
                                                "
                                                >{{
                                                    errores
                                                        .direccion_velacion[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full xl:w-6/12 px-2 h-full py-4">
                            <div class="form-group py-6">
                                <div class="title-form-group">Cremación</div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2 text-center"
                                        >
                                            <label>¿Cremar Cuerpo?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    vs-name="cremacion_b"
                                                    v-model="form.cremacion_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    vs-name="cremacion_b"
                                                    v-model="form.cremacion_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-9/12 px-2"
                                        >
                                            <label>
                                                Fecha de la Cremación
                                                <span
                                                    v-if="form.cremacion_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <flat-pickr
                                                name="fechahora_cremacion"
                                                data-vv-as=" "
                                                v-validate:fechahora_cremacion_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="
                                                    configdateTimePickerWithTime
                                                "
                                                v-model="
                                                    form.fechahora_cremacion
                                                "
                                                placeholder="Fecha y Hora de Cremación"
                                                class="w-full"
                                                :disabled="
                                                    form.cremacion_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "fechahora_cremacion"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .fechahora_cremacion
                                                "
                                                >{{
                                                    errores
                                                        .fechahora_cremacion[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Fecha de Entrega de Cenizas
                                                <span
                                                    v-if="form.cremacion_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <flat-pickr
                                                name="fechahora_entrega_cenizas"
                                                data-vv-as=" "
                                                v-validate:fechahora_entrega_cenizas_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="
                                                    configdateTimePickerWithTime
                                                "
                                                v-model="
                                                    form.fechahora_entrega_cenizas
                                                "
                                                placeholder="Fecha y hora para entrega de cenizas"
                                                class="w-full"
                                                :disabled="
                                                    form.cremacion_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "fechahora_entrega_cenizas"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .fechahora_entrega_cenizas
                                                "
                                                >{{
                                                    errores
                                                        .fechahora_entrega_cenizas[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Descripción de Urna</label>
                                            <vs-input
                                                name="descripcion_urna"
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Descripción de urna"
                                                v-model="form.descripcion_urna"
                                                :disabled="
                                                    form.cremacion_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "descripcion_urna"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .descripcion_urna
                                                "
                                                >{{
                                                    errores.descripcion_urna[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full xl:w-6/12 px-2 h-full py-4">
                            <div class="form-group py-6">
                                <div class="title-form-group">Inhumación</div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2 text-center"
                                        >
                                            <label>¿Inhumar Cuerpo?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    vs-name="inhumacion_b"
                                                    v-model="form.inhumacion_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    vs-name="inhumacion_b"
                                                    v-model="form.inhumacion_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-5/12 px-2"
                                        >
                                            <label>
                                                ¿Lugar de Inhumación?
                                                <span
                                                    v-if="
                                                        form.inhumacion_b == 1
                                                    "
                                                    >(*)</span
                                                >
                                            </label>
                                            <v-select
                                                :options="cementerios_servicio"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="
                                                    form.cementerio_servicio
                                                "
                                                class="w-full"
                                                v-validate:cementerio_servicio_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="cementerio_servicio"
                                                data-vv-as=" "
                                                :disabled="
                                                    form.inhumacion_b != 1
                                                        ? true
                                                        : false
                                                "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>

                                            <span>
                                                {{
                                                    errors.first(
                                                        "cementerio_servicio"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores[
                                                        'cementerio_servicio.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "cementerio_servicio.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-4/12 px-2"
                                        >
                                            <label>
                                                Fecha Inhumación
                                                <span
                                                    v-if="
                                                        form.inhumacion_b == 1
                                                    "
                                                    >(*)</span
                                                >
                                            </label>
                                            <flat-pickr
                                                name="fechahora_inhumacion"
                                                data-vv-as=" "
                                                v-validate:fechahora_inhumacion_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="
                                                    configdateTimePickerWithTime
                                                "
                                                v-model="
                                                    form.fechahora_inhumacion
                                                "
                                                placeholder="Fecha y hora de inhumación"
                                                class="w-full"
                                                :disabled="
                                                    form.inhumacion_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "fechahora_inhumacion"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .fechahora_inhumacion
                                                "
                                                >{{
                                                    errores
                                                        .fechahora_inhumacion[0]
                                                }}</span
                                            >
                                        </div>

                                        <div
                                            v-show="
                                                this.form.cementerio_servicio
                                                    .value == 1
                                            "
                                            class="w-full input-text px-2"
                                        >
                                            <!--cementerio Aeternus-->
                                            <div
                                                class="w-full"
                                                v-if="fueCancelada"
                                            >
                                                <div
                                                    class="theme-background text-center mt-3 py-2 px-2 size-base border-gray-solid-1"
                                                >
                                                    <div class="flex flex-wrap">
                                                        <div
                                                            class="w-full lg:w-10/12 py-1 px-2"
                                                        >
                                                            <span
                                                                class="font-medium"
                                                            >
                                                                Ubicación:
                                                            </span>
                                                            {{
                                                                form.ubicacion_convenio
                                                            }}
                                                            <span
                                                                class="font-medium"
                                                            >
                                                                $ Saldo por
                                                                pagar:
                                                            </span>
                                                            $
                                                            {{
                                                                this
                                                                    .saldo_neto_terreno
                                                                    | numFormat(
                                                                        "0,000.00"
                                                                    )
                                                            }}
                                                            MXN
                                                        </div>
                                                        <div
                                                            class="w-full lg:w-2/12 text-center py-1"
                                                        >
                                                            <span
                                                                class="color-danger-900 cursor-pointer"
                                                                >X Cambiar
                                                                convenio
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="w-full"
                                                v-else-if="
                                                    this.form
                                                        .ventas_terrenos_id ==
                                                        0 &&
                                                    this.form
                                                        .ventas_terrenos_id ==
                                                        ''
                                                "
                                            >
                                                <div
                                                    class="bg-danger-50 text-center py-2 mt-3 size-base border-danger-solid-1 cursor-pointer color-danger-900"
                                                    @click="
                                                        openBuscadorTerreno = true
                                                    "
                                                >
                                                    Seleccione la propiedad
                                                </div>
                                            </div>
                                            <div class="w-full" v-else>
                                                <div
                                                    class="bg-success-50 py-2 mt-3 size-base border-success-solid-2 uppercase"
                                                >
                                                    <div class="flex flex-wrap">
                                                        <div
                                                            class="w-full lg:w-10/12 py-1 px-2"
                                                        >
                                                            <span
                                                                class="font-medium"
                                                            >
                                                                Ubicación:
                                                            </span>
                                                            {{
                                                                form.ubicacion_convenio
                                                            }}
                                                            <span
                                                                class="font-medium"
                                                            >
                                                                $ Saldo por
                                                                pagar:
                                                            </span>
                                                            $
                                                            {{
                                                                this
                                                                    .saldo_neto_terreno
                                                                    | numFormat(
                                                                        "0,000.00"
                                                                    )
                                                            }}
                                                            MXN
                                                        </div>
                                                        <div
                                                            class="w-full lg:w-2/12 text-center py-1"
                                                        >
                                                            <span
                                                                @click="
                                                                    quitarTerreno()
                                                                "
                                                                class="color-danger-900 cursor-pointer"
                                                                >X Cambiar
                                                                convenio
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Fin cementerio Aeternus-->
                                        </div>

                                        <!--otro cementerio-->
                                        <div
                                            v-show="
                                                this.form.cementerio_servicio
                                                    .value != 1
                                            "
                                            class="w-full input-text px-2"
                                        >
                                            <label>
                                                Cementerio y Ubicación
                                                (Cementerio, Fila, Lote y
                                                Sección)
                                                <span
                                                    v-if="
                                                        form.inhumacion_b ==
                                                            1 &&
                                                        form.cementerio_servicio
                                                            .value > 1
                                                    "
                                                    >(*)</span
                                                >
                                            </label>
                                            <vs-input
                                                name="ubicacion"
                                                data-vv-as=" "
                                                v-validate:ubicacion_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Ubicación del terreno"
                                                v-model="form.ubicacion"
                                                :disabled="
                                                    form.inhumacion_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{ errors.first("ubicacion") }}
                                            </span>

                                            <span
                                                v-if="this.errores.ubicacion"
                                                >{{
                                                    errores.ubicacion[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full xl:w-6/12 px-2 h-full py-4">
                            <div class="form-group py-6">
                                <div class="title-form-group">Traslado</div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2 text-center"
                                        >
                                            <label>¿Trasladar Fallecido?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    vs-name="traslado_b"
                                                    v-model="form.traslado_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    vs-name="traslado_b"
                                                    v-model="form.traslado_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-9/12 px-2"
                                        >
                                            <label>
                                                Fecha del Traslado
                                                <span
                                                    v-if="form.traslado_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <flat-pickr
                                                name="fechahora_traslado"
                                                data-vv-as=" "
                                                v-validate:fechahora_traslado_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="
                                                    configdateTimePickerWithTime
                                                "
                                                v-model="
                                                    form.fechahora_traslado
                                                "
                                                placeholder="Fecha y hora del traslado"
                                                class="w-full"
                                                :disabled="
                                                    form.traslado_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "fechahora_traslado"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .fechahora_traslado
                                                "
                                                >{{
                                                    errores
                                                        .fechahora_traslado[0]
                                                }}</span
                                            >
                                        </div>
                                        <div class="w-full input-text px-2">
                                            <label>
                                                Lugar del Traslado
                                                <span
                                                    v-if="form.traslado_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <vs-input
                                                name="destino_traslado"
                                                data-vv-as=" "
                                                v-validate:destino_traslado_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Dirección a trasladar"
                                                v-model="form.destino_traslado"
                                                :disabled="
                                                    form.traslado_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "destino_traslado"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .destino_traslado
                                                "
                                                >{{
                                                    errores.destino_traslado[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="w-full xl:w-6/12 px-2 h-full py-4"
                            v-show="!esExhumacion"
                        >
                            <div class="form-group py-6">
                                <div class="title-form-group">Aseguradora</div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-4/12 px-2 text-center"
                                        >
                                            <label
                                                >¿Servicio con
                                                Aseguradora?</label
                                            >
                                            <div class="mt-3">
                                                <vs-radio
                                                    vs-name="aseguradora_b"
                                                    v-model="form.aseguradora_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    vs-name="aseguradora_b"
                                                    v-model="form.aseguradora_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-8/12 px-2"
                                        >
                                            <label>Num. Convenio</label>
                                            <vs-input
                                                name="numero_convenio_aseguradora"
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Núm. Convenio de referencia"
                                                v-model="
                                                    form.numero_convenio_aseguradora
                                                "
                                                :disabled="
                                                    form.aseguradora_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "numero_convenio_aseguradora"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .numero_convenio_aseguradora
                                                "
                                                >{{
                                                    errores
                                                        .numero_convenio_aseguradora[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Aseguradora
                                                <span
                                                    v-if="
                                                        form.aseguradora_b == 1
                                                    "
                                                    >(*)</span
                                                >
                                            </label>
                                            <vs-input
                                                name="aseguradora"
                                                data-vv-as=" "
                                                v-validate:aseguradora_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre de la aseguradora"
                                                v-model="form.aseguradora"
                                                :disabled="
                                                    form.aseguradora_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first("aseguradora")
                                                }}
                                            </span>

                                            <span
                                                v-if="this.errores.aseguradora"
                                                >{{
                                                    errores.aseguradora[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Teléfono (s)</label>
                                            <vs-input
                                                name="telefono_aseguradora"
                                                maxlength="45"
                                                type="text"
                                                class="w-full"
                                                placeholder="Teléfonos de la aseguradora"
                                                v-model="
                                                    form.telefono_aseguradora
                                                "
                                                :disabled="
                                                    form.aseguradora_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "telefono_aseguradora"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .telefono_aseguradora
                                                "
                                                >{{
                                                    errores
                                                        .telefono_aseguradora[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="w-full xl:w-6/12 px-2 h-full py-4"
                            v-show="!esExhumacion"
                        >
                            <div class="form-group py-6">
                                <div class="title-form-group">
                                    Misa / Ceremonia
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2 text-center"
                                        >
                                            <label>¿Ceremonia o Misa?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    vs-name="misa_b"
                                                    v-model="form.misa_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    vs-name="misa_b"
                                                    v-model="form.misa_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-9/12 px-2"
                                        >
                                            <label>
                                                Fecha y Hora
                                                <span v-if="form.misa_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <flat-pickr
                                                name="fechahora_misa"
                                                data-vv-as=" "
                                                v-validate:fechahora_misa_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="
                                                    configdateTimePickerWithTime
                                                "
                                                v-model="form.fechahora_misa"
                                                placeholder="Fecha y hora de la misa"
                                                class="w-full"
                                                :disabled="
                                                    form.misa_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "fechahora_misa"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores.fechahora_misa
                                                "
                                                >{{
                                                    errores.fechahora_misa[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>
                                                Iglesia o Templo
                                                <span v-if="form.misa_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <vs-input
                                                name="iglesia_misa"
                                                data-vv-as=" "
                                                v-validate:iglesia_misa_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="75"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre de la iglesia"
                                                v-model="form.iglesia_misa"
                                                :disabled="
                                                    form.misa_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first("iglesia_misa")
                                                }}
                                            </span>

                                            <span
                                                v-if="this.errores.iglesia_misa"
                                                >{{
                                                    errores.iglesia_misa[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Dirección</label>
                                            <vs-input
                                                name="direccion_iglesia"
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Dirección de la iglesia"
                                                v-model="form.direccion_iglesia"
                                                :disabled="
                                                    form.misa_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{
                                                    errors.first(
                                                        "direccion_iglesia"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores
                                                        .direccion_iglesia
                                                "
                                                >{{
                                                    errores.direccion_iglesia[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="w-full xl:w-6/12 px-2 h-full py-4"
                            v-show="!esExhumacion"
                        >
                            <div class="form-group py-6">
                                <div class="title-form-group">
                                    Cadena de Custodia
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2 text-center"
                                        >
                                            <label>¿Requirió Custodia?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    vs-name="custodia_b"
                                                    v-model="form.custodia_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    vs-name="custodia_b"
                                                    v-model="form.custodia_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-9/12 px-2"
                                        >
                                            <label
                                                >Nombre del Responsable</label
                                            >
                                            <vs-input
                                                name="responsable_custodia"
                                                maxlength="150"
                                                type="text"
                                                class="w-full"
                                                placeholder="Nombre del responsable"
                                                v-model="
                                                    form.responsable_custodia
                                                "
                                                :disabled="
                                                    form.custodia_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "responsable_custodia"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores
                                                        .responsable_custodia
                                                "
                                                >{{
                                                    errores
                                                        .responsable_custodia[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Folio de Referencia</label>
                                            <vs-input
                                                name="folio_custodia"
                                                maxlength="45"
                                                type="text"
                                                class="w-full"
                                                placeholder="Folio de la cadena de custodia"
                                                v-model="form.folio_custodia"
                                                :disabled="
                                                    form.custodia_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "folio_custodia"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores.folio_custodia
                                                "
                                                >{{
                                                    errores.folio_custodia[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-6/12 px-2"
                                        >
                                            <label>Folio de Liberación</label>
                                            <vs-input
                                                name="folio_liberacion"
                                                maxlength="45"
                                                type="text"
                                                class="w-full"
                                                placeholder="Folio del acta de liberación"
                                                v-model="form.folio_liberacion"
                                                :disabled="
                                                    form.custodia_b != 1
                                                        ? true
                                                        : false
                                                "
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "folio_liberacion"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores
                                                        .folio_liberacion
                                                "
                                                >{{
                                                    errores.folio_liberacion[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--contenido de los destinos del servicio-->
                </div>

                <div class="tab-content" v-show="activeTab == 3">
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <div class="form-group py-6">
                                <div class="title-form-group">
                                    Equipo Para la Velación
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div class="w-full input-text px-2">
                                            <label
                                                >¿Requirió Equipo de
                                                Velación?</label
                                            >
                                            <div class="mt-3">
                                                <vs-radio
                                                    :disabled="esExhumacion"
                                                    vs-name="material_velacion_b"
                                                    v-model="
                                                        form.material_velacion_b
                                                    "
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    :disabled="esExhumacion"
                                                    vs-name="material_velacion_b"
                                                    v-model="
                                                        form.material_velacion_b
                                                    "
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div class="w-full px-2 input-text">
                                            <vs-table
                                                class="tabla-datos"
                                                :data="form.material_velacion"
                                                noDataText="No se han agregado Artículos"
                                            >
                                                <template slot="header">
                                                    <h3>
                                                        Artículos que se van a
                                                        rentar
                                                    </h3>
                                                </template>
                                                <template slot="thead">
                                                    <vs-th>Artículo</vs-th>
                                                    <vs-th>Nota</vs-th>
                                                    <vs-th>Cantidad</vs-th>
                                                </template>
                                                <template slot-scope="{ data }">
                                                    <vs-tr
                                                        :data="tr"
                                                        :key="indextr"
                                                        v-for="(
                                                            tr, indextr
                                                        ) in data"
                                                    >
                                                        <vs-td class="w-4/12">
                                                            <div>
                                                                {{
                                                                    tr.descripcion
                                                                }}
                                                            </div>
                                                        </vs-td>
                                                        <vs-td class="w-4/12">
                                                            <vs-input
                                                                :name="
                                                                    'nota_material' +
                                                                    indextr
                                                                "
                                                                class="w-full mr-auto ml-auto cantidad"
                                                                maxlength="180"
                                                                v-model="
                                                                    form
                                                                        .material_velacion[
                                                                        indextr
                                                                    ].nota
                                                                "
                                                                :disabled="
                                                                    form.material_velacion_b ==
                                                                    0
                                                                        ? true
                                                                        : false
                                                                "
                                                            />
                                                        </vs-td>
                                                        <vs-td class="w-4/12">
                                                            <vs-input
                                                                :name="
                                                                    'cantidad' +
                                                                    indextr
                                                                "
                                                                data-vv-as=" "
                                                                data-vv-validate-on="blur"
                                                                v-validate="
                                                                    'required|integer|min_value:' +
                                                                    0
                                                                "
                                                                class="w-full mr-auto ml-auto cantidad"
                                                                maxlength="4"
                                                                v-model="
                                                                    form
                                                                        .material_velacion[
                                                                        indextr
                                                                    ].cantidad
                                                                "
                                                                :disabled="
                                                                    form.material_velacion_b ==
                                                                    0
                                                                        ? true
                                                                        : false
                                                                "
                                                            />

                                                            <span>{{
                                                                errors.first(
                                                                    "cantidad" +
                                                                        indextr
                                                                )
                                                            }}</span>

                                                            <span
                                                                v-if="
                                                                    errores[
                                                                        'material_velacion.' +
                                                                            indextr +
                                                                            '.cantidad'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    errores[
                                                                        "material_velacion." +
                                                                            indextr +
                                                                            ".cantidad"
                                                                    ][0]
                                                                }}
                                                            </span>
                                                        </vs-td>
                                                        <template
                                                            class="expand-user"
                                                            slot="expand"
                                                        ></template>
                                                    </vs-tr>
                                                </template>
                                            </vs-table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content" v-show="activeTab == 4">
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <div class="form-group py-6">
                                <div class="title-form-group">
                                    Acta de Defunción
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full xl:w-2/12 px-2 text-center input-text"
                                        >
                                            <label>¿Se Tramitó Acta?</label>
                                            <div class="mt-3">
                                                <vs-radio
                                                    vs-name="acta_b"
                                                    v-model="form.acta_b"
                                                    :vs-value="1"
                                                    class="mr-4"
                                                    >SI</vs-radio
                                                >
                                                <vs-radio
                                                    vs-name="acta_b"
                                                    v-model="form.acta_b"
                                                    :vs-value="0"
                                                    class="mr-4"
                                                    >NO</vs-radio
                                                >
                                            </div>
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-5/12 px-2"
                                        >
                                            <label>
                                                Folio del Acta
                                                <span v-if="form.acta_b == 1"
                                                    >(*)</span
                                                >
                                            </label>

                                            <vs-input
                                                name="folio_acta"
                                                data-vv-as=" "
                                                v-validate:folio_acta_validacion_computed.immediate="
                                                    'required'
                                                "
                                                maxlength="45"
                                                type="text"
                                                class="w-full"
                                                placeholder="Folio del Acta"
                                                v-model="form.folio_acta"
                                                :disabled="
                                                    this.form.acta_b == 0
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{ errors.first("folio_acta") }}
                                            </span>

                                            <span
                                                v-if="this.errores.folio_acta"
                                                >{{
                                                    errores.folio_acta[0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-5/12 px-2"
                                        >
                                            <label>
                                                Fecha de Levantamiento
                                                <span v-if="form.acta_b == 1"
                                                    >(*)</span
                                                >
                                            </label>
                                            <flat-pickr
                                                name="fecha_acta"
                                                data-vv-as=" "
                                                v-validate:fecha_acta_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="configdateTimePicker"
                                                v-model="form.fecha_acta"
                                                placeholder="Fecha de Levantamiento"
                                                class="w-full"
                                                :disabled="
                                                    this.form.acta_b == 0
                                                        ? true
                                                        : false
                                                "
                                            />

                                            <span>
                                                {{ errors.first("fecha_acta") }}
                                            </span>

                                            <span
                                                v-if="this.errores.fecha_acta"
                                                >{{
                                                    errores.fecha_acta[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content" v-show="activeTab == 5">
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <div class="form-group py-6">
                                <div class="title-form-group">
                                    Uso de Convenios en el Servicio
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            v-if="verUsoConvenios"
                                            class="w-full mt-5"
                                        >
                                            <div class="flex flex-wrap">
                                                <div
                                                    class="w-full mb-6"
                                                    v-if="
                                                        form.id_convenio_plan >
                                                            0 &&
                                                        form.id_convenio_plan !=
                                                            ''
                                                    "
                                                >
                                                    <div
                                                        class="w-full"
                                                        v-if="
                                                            contratos_con_uso_plan_funerario_futuro_seleccionado.length >
                                                            0
                                                        "
                                                    >
                                                        <div
                                                            class="w-full alerta pt-4 pb-6 px-2"
                                                        >
                                                            <div class="info">
                                                                <h3>
                                                                    Uso de plan
                                                                    funerario a
                                                                    futuro
                                                                </h3>
                                                                <p>
                                                                    A
                                                                    continuación,
                                                                    se muestra
                                                                    la lista de
                                                                    finados que
                                                                    han
                                                                    utilizado el
                                                                    plan
                                                                    funerario a
                                                                    futuro
                                                                    seleccionado
                                                                    en este
                                                                    servicio.
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="w-full px-2 pb-6"
                                                            v-if="
                                                                datosPlanFunerario !=
                                                                []
                                                            "
                                                        >
                                                            <div
                                                                class="bg-success-50 py-2 size-base border-success-solid-2 uppercase"
                                                            >
                                                                <div
                                                                    class="flex flex-wrap"
                                                                >
                                                                    <div
                                                                        class="w-full py-1 px-2"
                                                                    >
                                                                        <span
                                                                            class="font-medium"
                                                                        >
                                                                            Plan
                                                                            Funerario
                                                                            a
                                                                            Futuro:
                                                                        </span>
                                                                        {{
                                                                            form.plan
                                                                        }}
                                                                        <span
                                                                            class="font-medium"
                                                                        >
                                                                            $
                                                                            Saldo
                                                                            por
                                                                            pagar:
                                                                        </span>
                                                                        $
                                                                        {{
                                                                            datosPlanFunerario.saldo_neto
                                                                                | numFormat(
                                                                                    "0,000.00"
                                                                                )
                                                                        }}
                                                                        MXN
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="w-full px-2"
                                                        >
                                                            <vs-table
                                                                class="tabla-datos"
                                                                :data="
                                                                    contratos_con_uso_plan_funerario_futuro_seleccionado
                                                                "
                                                                noDataText=""
                                                            >
                                                                <template
                                                                    slot="header"
                                                                >
                                                                    <h3>
                                                                        Lista de
                                                                        Finados
                                                                    </h3>
                                                                </template>
                                                                <template
                                                                    slot="thead"
                                                                >
                                                                    <vs-th
                                                                        >Fallecido</vs-th
                                                                    >
                                                                    <vs-th
                                                                        >Contratante</vs-th
                                                                    >
                                                                    <vs-th
                                                                        >Fecha
                                                                        de
                                                                        Uso</vs-th
                                                                    >
                                                                </template>
                                                                <template
                                                                    slot-scope="{
                                                                        data,
                                                                    }"
                                                                >
                                                                    <vs-tr
                                                                        :data="
                                                                            tr
                                                                        "
                                                                        :key="
                                                                            indextr
                                                                        "
                                                                        v-for="(
                                                                            tr,
                                                                            indextr
                                                                        ) in data"
                                                                        v-show="
                                                                            tr.status_b ==
                                                                            1
                                                                        "
                                                                    >
                                                                        <vs-td>
                                                                            <div
                                                                                v-if="
                                                                                    get_id_solicitud ==
                                                                                    tr.id
                                                                                "
                                                                            >
                                                                                {{
                                                                                    tr.nombre_afectado +
                                                                                    " (Finado de este contrato)"
                                                                                }}
                                                                            </div>
                                                                            <div
                                                                                v-else
                                                                            >
                                                                                {{
                                                                                    tr.nombre_afectado
                                                                                }}
                                                                            </div>
                                                                        </vs-td>
                                                                        <vs-td>
                                                                            <div>
                                                                                {{
                                                                                    tr
                                                                                        .operacion
                                                                                        .cliente
                                                                                        .nombre
                                                                                }}
                                                                            </div>
                                                                        </vs-td>
                                                                        <vs-td>
                                                                            <div>
                                                                                {{
                                                                                    tr
                                                                                        .operacion
                                                                                        .fecha_operacion_texto
                                                                                }}
                                                                            </div>
                                                                        </vs-td>
                                                                    </vs-tr>
                                                                </template>
                                                            </vs-table>
                                                        </div>
                                                    </div>

                                                    <div v-else>
                                                        <div
                                                            class="w-full alerta pt-4 pb-6 px-2"
                                                        >
                                                            <div
                                                                class="success"
                                                            >
                                                                <h3>
                                                                    Uso de plan
                                                                    funerario a
                                                                    futuro
                                                                </h3>
                                                                <p>
                                                                    El plan
                                                                    funerario de
                                                                    uso a futuro
                                                                    seleccionado
                                                                    no ha sido
                                                                    utilizado
                                                                    anteriormente.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--fin de contenido del plan funerario-->
                                                </div>
                                            </div>

                                            <!--Uso de cementerio-->
                                            <div
                                                class="w-full"
                                                v-if="
                                                    contratos_con_uso_terreno_seleccionado.length >
                                                    0
                                                "
                                            >
                                                <div
                                                    class="w-full alerta pt-4 pb-6 px-2"
                                                >
                                                    <div class="info">
                                                        <h3>
                                                            Uso de propiedad en
                                                            cementerio
                                                        </h3>
                                                        <p>
                                                            A continuación, se
                                                            muestra la lista de
                                                            finados dentro de la
                                                            propiedad en
                                                            cementerio
                                                            seleccionada en este
                                                            servicio.
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="w-full px-2">
                                                    <div
                                                        class="bg-success-50 py-2 size-base border-success-solid-2 uppercase"
                                                    >
                                                        <div
                                                            class="flex flex-wrap"
                                                        >
                                                            <div
                                                                class="w-full py-1 px-2"
                                                            >
                                                                <span
                                                                    class="font-medium"
                                                                >
                                                                    Ubicación:
                                                                </span>
                                                                {{
                                                                    form.ubicacion_convenio
                                                                }}
                                                                <span
                                                                    class="font-medium"
                                                                >
                                                                    $ Saldo por
                                                                    pagar:
                                                                </span>
                                                                $
                                                                {{
                                                                    this
                                                                        .saldo_neto_terreno
                                                                        | numFormat(
                                                                            "0,000.00"
                                                                        )
                                                                }}
                                                                MXN
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w-full px-2 pt-6">
                                                    <vs-table
                                                        class="tabla-datos"
                                                        :data="
                                                            contratos_con_uso_terreno_seleccionado
                                                        "
                                                        noDataText=""
                                                    >
                                                        <template slot="header">
                                                            <h3>
                                                                Lista de Finados
                                                            </h3>
                                                        </template>
                                                        <template slot="thead">
                                                            <vs-th
                                                                >Fallecido</vs-th
                                                            >
                                                            <vs-th
                                                                >Contratante</vs-th
                                                            >
                                                            <vs-th
                                                                >Fecha de
                                                                Uso</vs-th
                                                            >
                                                        </template>
                                                        <template
                                                            slot-scope="{
                                                                data,
                                                            }"
                                                        >
                                                            <vs-tr
                                                                :data="tr"
                                                                :key="indextr"
                                                                v-for="(
                                                                    tr, indextr
                                                                ) in data"
                                                                v-show="
                                                                    tr.status_b ==
                                                                    1
                                                                "
                                                            >
                                                                <vs-td>
                                                                    <div
                                                                        v-if="
                                                                            get_id_solicitud ==
                                                                            tr.id
                                                                        "
                                                                    >
                                                                        {{
                                                                            tr.nombre_afectado +
                                                                            " (Finado de este contrato)"
                                                                        }}
                                                                    </div>
                                                                    <div v-else>
                                                                        {{
                                                                            tr.nombre_afectado
                                                                        }}
                                                                    </div>
                                                                </vs-td>
                                                                <vs-td>
                                                                    <div>
                                                                        {{
                                                                            tr
                                                                                .operacion
                                                                                .cliente
                                                                                .nombre
                                                                        }}
                                                                    </div>
                                                                </vs-td>
                                                                <vs-td>
                                                                    <div>
                                                                        {{
                                                                            tr
                                                                                .operacion
                                                                                .fecha_operacion_texto
                                                                        }}
                                                                    </div>
                                                                </vs-td>
                                                            </vs-tr>
                                                        </template>
                                                    </vs-table>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div
                                                    class="w-full alerta pt-4 pb-6 px-2"
                                                >
                                                    <div class="success">
                                                        <h3>
                                                            Uso de propiedad en
                                                            cementerio
                                                        </h3>
                                                        <p>
                                                            La ubicación del
                                                            cementerio que ha
                                                            seleccionado no ha
                                                            sido utilizado
                                                            anteriormente.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Uso de cementerio-->
                                        </div>
                                        <div
                                            class="w-full input-text px-2 mb-6"
                                            v-else
                                        >
                                            <div
                                                class="w-full alerta pt-4 pb-6 px-2"
                                            >
                                                <div class="primary">
                                                    <h3>Uso de convenios</h3>
                                                    <p>
                                                        No se tiene registrado
                                                        el uso de propiedades de
                                                        cementerio ni planes
                                                        funerarios a futuro para
                                                        este contrato.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content" v-show="activeTab == 6">
                    <!--Contrato-->
                    <div class="flex flex-wrap">
                        <div class="w-full py-4">
                            <div class="form-group py-6">
                                <div class="title-form-group">
                                    Información del Contrato
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full xl:w-2/12 px-2 input-text"
                                        >
                                            <label>
                                                Fecha del Contrato
                                                <span>(*)</span>
                                            </label>
                                            <flat-pickr
                                                name="fechahora_contrato"
                                                data-vv-as=" "
                                                v-validate:fechahora_contrato_validacion_computed.immediate="
                                                    'required'
                                                "
                                                :config="
                                                    configdateTimePickerWithTime
                                                "
                                                v-model="
                                                    form.fechahora_contrato
                                                "
                                                placeholder="Fecha y Hora del Contrato"
                                                class="w-full"
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "fechahora_contrato"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores
                                                        .fechahora_contrato
                                                "
                                                >{{
                                                    errores
                                                        .fechahora_contrato[0]
                                                }}</span
                                            >
                                        </div>

                                        <div class="w-full xl:w-7/12">
                                            <div
                                                class="w-full px-2 input-text"
                                                v-if="fueCancelada"
                                            >
                                                <label>
                                                    Contratante
                                                    <span>(*)</span>
                                                </label>
                                                <div
                                                    class="theme-background text-center py-2 px-2 size-base border-gray-solid-1"
                                                >
                                                    <span class="font-medium">
                                                        Clave:
                                                    </span>
                                                    {{ form.id_cliente }},
                                                    <span class="font-medium">
                                                        Nombre:
                                                    </span>
                                                    {{ form.cliente }},
                                                    <span class="font-medium">
                                                        Dirección:
                                                    </span>
                                                    {{ form.direccion_cliente }}
                                                </div>
                                            </div>
                                            <div
                                                class="w-full px-2 input-text"
                                                v-else-if="
                                                    form.id_cliente == ''
                                                "
                                            >
                                                <label>
                                                    Contratante
                                                    <span>(*)</span>
                                                </label>
                                                <div
                                                    class="bg-danger-50 text-center py-2 px-2 size-base border-danger-solid-1 cursor-pointer color-danger-900"
                                                    @click="openBuscador = true"
                                                >
                                                    Click para seleccionar al
                                                    contratante
                                                </div>
                                            </div>
                                            <div
                                                class="w-full px-2 input-text"
                                                v-else
                                            >
                                                <label>
                                                    Contratante
                                                    <span>(*)</span>
                                                </label>
                                                <div
                                                    class="bg-success-50 py-2 px-2 size-base border-success-solid-2 uppercase"
                                                >
                                                    <div class="flex flex-wrap">
                                                        <div
                                                            class="w-full xl:w-8/12"
                                                        >
                                                            <span
                                                                class="font-medium"
                                                            >
                                                                Clave:
                                                            </span>
                                                            {{
                                                                form.id_cliente
                                                            }},
                                                            <span
                                                                class="font-medium"
                                                            >
                                                                Nombre:
                                                            </span>
                                                            {{ form.cliente }}
                                                            <span
                                                                class="font-medium hidden"
                                                            >
                                                                Dirección:
                                                            </span>
                                                            <span
                                                                class="hidden"
                                                            >
                                                                {{
                                                                    form.direccion_cliente
                                                                }}</span
                                                            >
                                                        </div>
                                                        <div
                                                            class="w-full xl:w-4/12 text-center xl:text-right"
                                                        >
                                                            <span
                                                                @click="
                                                                    quitarCliente()
                                                                "
                                                                class="color-danger-900 cursor-pointer"
                                                                >X Cambiar
                                                                cliente
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="w-full input-text xl:w-3/12 px-2"
                                        >
                                            <label
                                                >Parentesco con el
                                                Fallecido</label
                                            >
                                            <vs-input
                                                name="parentesco_contratante"
                                                maxlength="45"
                                                type="text"
                                                class="w-full"
                                                placeholder="Parentesco con el Fallecido"
                                                v-model="
                                                    form.parentesco_contratante
                                                "
                                            />
                                            <span>
                                                {{
                                                    errors.first(
                                                        "parentesco_contratante"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores
                                                        .parentesco_contratante
                                                "
                                                >{{
                                                    errores
                                                        .parentesco_contratante[0]
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--Uso de Planes-->

                            <div class="form-group" v-show="!esExhumacion">
                                <div class="title-form-group">
                                    Uso de Planes Funerarios
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full xl:w-2/12 px-2 input-text"
                                        >
                                            <label>
                                                ¿Tiene Plan Funerario?
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :options="sino"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="
                                                    form.plan_funerario_futuro_b
                                                "
                                                class="w-full"
                                                name="plan_funerario_futuro_b"
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>

                                            <span>
                                                {{
                                                    errors.first(
                                                        "plan_funerario_futuro_b"
                                                    )
                                                }}
                                            </span>

                                            <span
                                                v-if="
                                                    this.errores[
                                                        'plan_funerario_futuro_b.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "plan_funerario_futuro_b.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2"
                                            v-if="
                                                form.plan_funerario_futuro_b
                                                    .value == 0
                                            "
                                        >
                                            <label>
                                                ¿Usar Plan de Uso inmediato?
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :options="sino"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="
                                                    form.plan_funerario_inmediato_b
                                                "
                                                class="w-full"
                                                name="plan_funerario_inmediato_b"
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first(
                                                        "plan_funerario_inmediato_b"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'plan_funerario_inmediato_b.value'
                                                    ]
                                                "
                                            >
                                                {{
                                                    errores[
                                                        "plan_funerario_inmediato_b.value"
                                                    ][0]
                                                }}
                                            </span>
                                        </div>

                                        <div
                                            class="w-full xl:w-7/12 px-2 input-text"
                                            v-if="
                                                form.plan_funerario_futuro_b
                                                    .value == 0
                                            "
                                        >
                                            <label>
                                                Planes Funerarios de Uso
                                                Inmediato
                                                <span
                                                    v-if="
                                                        form
                                                            .plan_funerario_inmediato_b
                                                            .value == 1
                                                    "
                                                    >(*)</span
                                                >
                                            </label>
                                            <v-select
                                                :options="planes_funerarios"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.plan_funerario"
                                                class="w-full"
                                                v-validate:plan_funerario_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="plan_funerario"
                                                data-vv-as=" "
                                                :disabled="
                                                    form
                                                        .plan_funerario_inmediato_b
                                                        .value == 0
                                                        ? true
                                                        : false
                                                "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first(
                                                        "plan_funerario"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'plan_funerario.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "plan_funerario.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>

                                        <!--Seleccionar plan funerario a futuro si tiene convenio-->
                                        <div
                                            class="w-full xl:w-7/12"
                                            v-show="
                                                form.plan_funerario_futuro_b
                                                    .value == 1
                                            "
                                        >
                                            <div
                                                class="w-full px-2 input-text"
                                                v-if="fueCancelada"
                                            >
                                                <label>
                                                    Convenio del plan funerario
                                                    a futuro
                                                    <span>(*)</span>
                                                </label>
                                                <div
                                                    class="theme-background text-center py-2 px-2 size-base border-gray-solid-1"
                                                >
                                                    <span class="font-medium">
                                                        Plan Funerario:
                                                    </span>
                                                    {{ form.plan }}
                                                </div>
                                            </div>
                                            <div
                                                class="w-full px-2 input-text"
                                                v-else-if="
                                                    form.id_convenio_plan == ''
                                                "
                                            >
                                                <label>
                                                    Convenio del plan funerario
                                                    a futuro
                                                    <span>(*)</span>
                                                </label>
                                                <div
                                                    class="bg-danger-50 text-center py-2 px-2 size-base border-danger-solid-1 cursor-pointer color-danger-900"
                                                    @click="
                                                        openBuscadorPlan = true
                                                    "
                                                >
                                                    Click para seleccionar al
                                                    convenio
                                                </div>
                                            </div>
                                            <div
                                                class="w-full px-2 input-text"
                                                v-else
                                            >
                                                <label>
                                                    Convenio del plan funerario
                                                    a futuro
                                                    <span>(*)</span>
                                                </label>
                                                <div
                                                    class="bg-success-50 py-2 px-2 size-base border-success-solid-2 uppercase"
                                                >
                                                    <div class="flex flex-wrap">
                                                        <div
                                                            class="w-full lg:w-7/12"
                                                        >
                                                            <span
                                                                class="font-medium"
                                                            >
                                                                Plan Funerario:
                                                            </span>
                                                            {{ form.plan }}
                                                        </div>
                                                        <div
                                                            class="w-full lg:w-4/12 text-center xl:text-right"
                                                        >
                                                            <span
                                                                @click="
                                                                    quitarPlan()
                                                                "
                                                                class="color-danger-900 cursor-pointer"
                                                                >X Cambiar
                                                                convenio
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--Fin de plan funerario con convenio-->
                                        <div
                                            class="w-full input-text xl:w-3/12 px-2"
                                            v-show="
                                                form.plan_funerario_futuro_b
                                                    .value == 1
                                            "
                                        >
                                            <label>
                                                Tipo de Contratante
                                                <span>(*)</span>
                                            </label>
                                            <v-select
                                                :options="tipos_contratante"
                                                :clearable="false"
                                                :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                                v-model="form.tipo_contratante"
                                                class="w-full"
                                                v-validate:tipo_contratante_validacion_computed.immediate="
                                                    'required'
                                                "
                                                name="tipo_contratante"
                                                data-vv-as=" "
                                            >
                                                <div slot="no-options">
                                                    Seleccione 1
                                                </div>
                                            </v-select>
                                            <span>
                                                {{
                                                    errors.first(
                                                        "tipo_contratante"
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    this.errores[
                                                        'tipo_contratante.value'
                                                    ]
                                                "
                                                >{{
                                                    errores[
                                                        "tipo_contratante.value"
                                                    ][0]
                                                }}</span
                                            >
                                        </div>
                                        <!--Ver Contenido del plan-->
                                        <div
                                            v-if="verLista"
                                            class="w-full input-text px-2 mt-6"
                                        >
                                            <vx-card
                                                no-radius
                                                collapse-action
                                                :title="
                                                    form.plan_funerario_futuro_b
                                                        .value == 1
                                                        ? form.plan
                                                        : form.plan_funerario
                                                              .label
                                                "
                                            >
                                                <vs-table
                                                    class="tabla-datos no-header"
                                                    :data="conceptos"
                                                    noDataText="No se han agregado Artículos ni Servicios"
                                                >
                                                    <template slot="thead">
                                                        <vs-th
                                                            >Artículo/Servicio</vs-th
                                                        >
                                                        <vs-th
                                                            >Aplicar en</vs-th
                                                        >
                                                    </template>
                                                    <template
                                                        slot-scope="{ data }"
                                                    >
                                                        <vs-tr
                                                            :data="tr"
                                                            :key="indextr"
                                                            v-for="(
                                                                tr, indextr
                                                            ) in data"
                                                        >
                                                            <vs-td>
                                                                <div>
                                                                    {{
                                                                        tr.concepto
                                                                    }}
                                                                </div>
                                                            </vs-td>
                                                            <vs-td>
                                                                <div>
                                                                    {{
                                                                        tr.aplicar
                                                                    }}
                                                                </div>
                                                            </vs-td>
                                                        </vs-tr>

                                                        <!--mostrar en caso de que se use un plan ya comprado para uso a futuro-->

                                                        <vs-tr
                                                            v-if="
                                                                form
                                                                    .plan_funerario_futuro_b
                                                                    .value == 1
                                                            "
                                                        >
                                                            <vs-td
                                                                class="w-7/12"
                                                            >
                                                                <div
                                                                    class="py-1 text-right pr-4 dato_servicio"
                                                                >
                                                                    Titular del
                                                                    Convenio
                                                                </div>
                                                            </vs-td>
                                                            <vs-td
                                                                class="w-2/12"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        datosPlanFunerario !=
                                                                        []
                                                                    "
                                                                    class="uppercase dato_servicio_valor"
                                                                >
                                                                    {{
                                                                        datosPlanFunerario.nombre
                                                                    }}
                                                                </div>
                                                            </vs-td>
                                                        </vs-tr>
                                                        <vs-tr
                                                            v-if="
                                                                form
                                                                    .plan_funerario_futuro_b
                                                                    .value == 1
                                                            "
                                                        >
                                                            <vs-td
                                                                class="w-7/12"
                                                            >
                                                                <div
                                                                    class="py-1 text-right pr-4 dato_servicio"
                                                                >
                                                                    Fecha de la
                                                                    Venta
                                                                </div>
                                                            </vs-td>
                                                            <vs-td
                                                                class="w-2/12"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        datosPlanFunerario !=
                                                                        []
                                                                    "
                                                                    class="dato_servicio_valor"
                                                                >
                                                                    {{
                                                                        datosPlanFunerario.fecha_operacion_texto
                                                                    }}
                                                                </div>
                                                            </vs-td>
                                                        </vs-tr>
                                                        <vs-tr
                                                            v-if="
                                                                form
                                                                    .plan_funerario_futuro_b
                                                                    .value == 1
                                                            "
                                                        >
                                                            <vs-td
                                                                class="w-7/12"
                                                            >
                                                                <div
                                                                    class="py-1 text-right pr-4 dato_servicio"
                                                                >
                                                                    Estado del
                                                                    Plan
                                                                </div>
                                                            </vs-td>
                                                            <vs-td
                                                                class="w-2/12"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        datosPlanFunerario !=
                                                                        []
                                                                    "
                                                                    class="dato_servicio_valor"
                                                                >
                                                                    <span
                                                                        class="color-warning-900"
                                                                        v-if="
                                                                            datosPlanFunerario.operacion_status ==
                                                                            1
                                                                        "
                                                                        >{{
                                                                            datosPlanFunerario.status_texto
                                                                        }}</span
                                                                    >
                                                                    <span
                                                                        class="color-success-900"
                                                                        v-else-if="
                                                                            datosPlanFunerario.operacion_status ==
                                                                            2
                                                                        "
                                                                        >{{
                                                                            datosPlanFunerario.status_texto
                                                                        }}</span
                                                                    >

                                                                    <span
                                                                        v-else-if="
                                                                            datosPlanFunerario.operacion_status ==
                                                                            0
                                                                        "
                                                                        >{{
                                                                            datosPlanFunerario.status_texto
                                                                        }}</span
                                                                    >
                                                                </div>
                                                            </vs-td>
                                                        </vs-tr>

                                                        <vs-tr
                                                            v-if="
                                                                form
                                                                    .plan_funerario_futuro_b
                                                                    .value == 1
                                                            "
                                                        >
                                                            <vs-td
                                                                class="w-7/12"
                                                            >
                                                                <div
                                                                    class="py-1 text-right pr-4 dato_servicio"
                                                                >
                                                                    Saldo
                                                                    Restante
                                                                </div>
                                                            </vs-td>
                                                            <vs-td
                                                                class="w-2/12"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        datosPlanFunerario !=
                                                                        []
                                                                    "
                                                                    class="dato_servicio_valor"
                                                                >
                                                                    $
                                                                    {{
                                                                        datosPlanFunerario.saldo_neto
                                                                            | numFormat(
                                                                                "0,000.00"
                                                                            )
                                                                    }}
                                                                </div>
                                                            </vs-td>
                                                        </vs-tr>

                                                        <!--fFIN DE DATOS A USAR EN CASO DE QUE SEA A USO A FUTURO-->

                                                        <!--fFIN DE DATOS A USAR EN CASO DE QUE SEA A USO INMEDIATO-->
                                                        <vs-tr
                                                            v-if="
                                                                form
                                                                    .plan_funerario_inmediato_b
                                                                    .value ==
                                                                    1 &&
                                                                form
                                                                    .plan_funerario_futuro_b
                                                                    .value ==
                                                                    0 &&
                                                                form
                                                                    .plan_funerario
                                                                    .value != ''
                                                            "
                                                        >
                                                            <vs-td
                                                                class="w-7/12"
                                                            >
                                                                <div
                                                                    class="py-1 text-right pr-4 dato_servicio"
                                                                >
                                                                    Plan
                                                                    Funerario de
                                                                    Uso
                                                                    Inmediato
                                                                </div>
                                                            </vs-td>
                                                            <vs-td
                                                                class="w-2/12"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        datosPlanFunerario !=
                                                                        []
                                                                    "
                                                                    class="dato_servicio_valor"
                                                                >
                                                                    {{
                                                                        form
                                                                            .plan_funerario
                                                                            .label
                                                                    }}
                                                                </div>
                                                            </vs-td>
                                                        </vs-tr>
                                                        <vs-tr
                                                            v-if="
                                                                form
                                                                    .plan_funerario_inmediato_b
                                                                    .value ==
                                                                    1 &&
                                                                form
                                                                    .plan_funerario_futuro_b
                                                                    .value ==
                                                                    0 &&
                                                                form
                                                                    .plan_funerario
                                                                    .value != ''
                                                            "
                                                        >
                                                            <vs-td
                                                                class="w-7/12"
                                                            >
                                                                <div
                                                                    class="py-1 text-right pr-4 dato_servicio"
                                                                >
                                                                    Total del
                                                                    Plan
                                                                    Funerario
                                                                </div>
                                                            </vs-td>
                                                            <vs-td
                                                                class="w-2/12"
                                                            >
                                                                <div
                                                                    v-if="
                                                                        datosPlanFunerario !=
                                                                        []
                                                                    "
                                                                    class="dato_servicio_valor"
                                                                >
                                                                    $
                                                                    {{
                                                                        costo_uso_inmediato_computed
                                                                            | numFormat(
                                                                                "0,000.00"
                                                                            )
                                                                    }}
                                                                </div>
                                                            </vs-td>
                                                        </vs-tr>
                                                        <!--FIN DE DATOS A USAR EN CASO DE QUE SEA A USO INMEDIATO-->
                                                    </template>
                                                </vs-table>
                                            </vx-card>
                                            <!--fin de contenido del plan funerario-->
                                        </div>
                                        <!--Fin de contenido-->
                                    </div>
                                </div>
                            </div>

                            <!-- Fin Uso de Planes-->

                            <!--Conceptos-->

                            <div class="form-group">
                                <div class="title-form-group">
                                    Desglose de Artículos y Servicios a Pagar
                                </div>
                                <div class="form-group-content">
                                    <div class="flex flex-wrap">
                                        <img
                                            class="img-btn-20 mx-3 mt-4 hidden lg:block"
                                            src="@assets/images/barcode.svg"
                                        />
                                        <div
                                            class="w-auto lg:w-4/12 xl:w-2/12 px-2 input-text hidden lg:block"
                                        >
                                            <label
                                                >Clave o código de barras</label
                                            >
                                            <vs-input
                                                ref="codigo_barras"
                                                name="codigo_barras"
                                                data-vv-as=" "
                                                type="text"
                                                class="w-full"
                                                placeholder="Ej. 0000000123"
                                                maxlength="28"
                                                v-model.trim="
                                                    serverOptions.numero_control
                                                "
                                                v-on:keyup.enter="
                                                    get_concepto_por_codigo(
                                                        'codigo_barras'
                                                    )
                                                "
                                                v-on:blur="
                                                    get_concepto_por_codigo(
                                                        'codigo_barras',
                                                        'blur'
                                                    )
                                                "
                                            />
                                        </div>
                                        <img
                                            class="cursor-pointer img-btn-20 mx-3 mt-4 hidden lg:block"
                                            src="@assets/images/searcharticulo.svg"
                                            title="Buscador de artículos y servicios"
                                            @click="
                                                openBuscadorArticulos = true
                                            "
                                        />

                                        <div
                                            class="w-full text-right block lg:hidden"
                                        >
                                            <vs-button
                                                class="sm:w-auto md:w-auto md:ml-2 my-2 md:mt-0"
                                                color="primary"
                                                @click="
                                                    openBuscadorArticulos = true
                                                "
                                            >
                                                <span>Buscar artículos</span>
                                            </vs-button>
                                        </div>

                                        <div class="w-full my-6 px-2">
                                            <vs-table
                                                class="tabla-datos"
                                                :data="form.articulos_servicios"
                                                noDataText="No se han agregado Artículos ni Servicios"
                                            >
                                                <template slot="header">
                                                    <h3>
                                                        Servicios y Artículos
                                                        que Incluye el Servicio
                                                        Funerario
                                                    </h3>
                                                </template>
                                                <template slot="thead">
                                                    <vs-th>#</vs-th>
                                                    <vs-th hidden>Clave</vs-th>
                                                    <vs-th hidden
                                                        >Código de Barras</vs-th
                                                    >
                                                    <vs-th hidden>Tipo</vs-th>
                                                    <vs-th>Descripción</vs-th>
                                                    <vs-th>Cant.</vs-th>
                                                    <vs-th>Costo Neto</vs-th>
                                                    <vs-th>Descuento</vs-th>
                                                    <vs-th
                                                        >Costo Neto Con
                                                        Descuento</vs-th
                                                    >
                                                    <vs-th>Importe</vs-th>
                                                    <vs-th
                                                        >Plan Funerario</vs-th
                                                    >
                                                    <vs-th>Facturable</vs-th>
                                                    <vs-th>Quitar</vs-th>
                                                </template>
                                                <template slot-scope="{ data }">
                                                    <vs-tr
                                                        :data="tr"
                                                        :key="indextr"
                                                        v-for="(
                                                            tr, indextr
                                                        ) in data"
                                                    >
                                                        <vs-td class="">
                                                            <div>
                                                                <span>{{
                                                                    indextr + 1
                                                                }}</span>
                                                            </div>
                                                        </vs-td>
                                                        <vs-td hidden>
                                                            <div>
                                                                <span>{{
                                                                    data[
                                                                        indextr
                                                                    ].id
                                                                }}</span>
                                                            </div>
                                                        </vs-td>
                                                        <vs-td hidden>
                                                            <div>
                                                                <span>{{
                                                                    data[
                                                                        indextr
                                                                    ]
                                                                        .codigo_barras
                                                                }}</span>
                                                            </div>
                                                        </vs-td>
                                                        <vs-td hidden>
                                                            <div>
                                                                {{
                                                                    data[
                                                                        indextr
                                                                    ].tipo
                                                                }}
                                                            </div>
                                                        </vs-td>
                                                        <vs-td class="">
                                                            <div
                                                                class="uppercase"
                                                            >
                                                                {{
                                                                    data[
                                                                        indextr
                                                                    ]
                                                                        .descripcion
                                                                }}
                                                            </div>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <vs-input
                                                                :name="
                                                                    'cantidad_articulos_servicios' +
                                                                    indextr
                                                                "
                                                                data-vv-as=" "
                                                                data-vv-validate-on="blur"
                                                                v-validate="
                                                                    'required|integer|min_value:' +
                                                                    1
                                                                "
                                                                class="mr-auto ml-auto input-cantidad"
                                                                maxlength="4"
                                                                v-model="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ].cantidad
                                                                "
                                                            />
                                                            <div
                                                                class="input-text"
                                                            >
                                                                <span>
                                                                    {{
                                                                        errors.first(
                                                                            "cantidad_articulos_servicios" +
                                                                                indextr
                                                                        )
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </vs-td>
                                                        <vs-td
                                                            class=""
                                                            v-if="
                                                                habilitar_plan_funerario_b ==
                                                                    false ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        1 &&
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ].plan_b ==
                                                                        0) ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        0 &&
                                                                    form
                                                                        .plan_funerario_inmediato_b
                                                                        .value ==
                                                                        1)
                                                            "
                                                        >
                                                            <vs-input
                                                                :name="
                                                                    'costo_neto_normal_articulos_servicios' +
                                                                    indextr
                                                                "
                                                                data-vv-as=" "
                                                                data-vv-validate-on="blur"
                                                                v-validate="
                                                                    'required|decimal:2|min_value:' +
                                                                    0
                                                                "
                                                                class="mr-auto ml-auto input-cantidad"
                                                                maxlength="10"
                                                                v-model="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .costo_neto_normal
                                                                "
                                                                :disabled="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .descuento_b ==
                                                                    1
                                                                "
                                                            />
                                                            <div
                                                                class="input-text"
                                                            >
                                                                <span>
                                                                    {{
                                                                        errors.first(
                                                                            "costo_neto_normal_articulos_servicios" +
                                                                                indextr
                                                                        )
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </vs-td>
                                                        <vs-td v-else class="">
                                                            <div>$ 0.00</div>
                                                        </vs-td>
                                                        <vs-td
                                                            class=""
                                                            v-if="
                                                                habilitar_plan_funerario_b ==
                                                                    false ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        1 &&
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ].plan_b ==
                                                                        0) ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        0 &&
                                                                    form
                                                                        .plan_funerario_inmediato_b
                                                                        .value ==
                                                                        1)
                                                            "
                                                        >
                                                            <vs-switch
                                                                class="ml-auto mr-auto"
                                                                color="success"
                                                                icon-pack="feather"
                                                                v-model="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .descuento_b
                                                                "
                                                            >
                                                                <span slot="on"
                                                                    >SI</span
                                                                >
                                                                <span slot="off"
                                                                    >NO</span
                                                                >
                                                            </vs-switch>
                                                        </vs-td>
                                                        <vs-td v-else class="">
                                                            <div>N/A</div>
                                                        </vs-td>
                                                        <vs-td
                                                            class=""
                                                            v-if="
                                                                habilitar_plan_funerario_b ==
                                                                    false ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        1 &&
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ].plan_b ==
                                                                        0) ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        0 &&
                                                                    form
                                                                        .plan_funerario_inmediato_b
                                                                        .value ==
                                                                        1)
                                                            "
                                                        >
                                                            <vs-input
                                                                :name="
                                                                    'costo_neto_descuento_articulos_servicios' +
                                                                    indextr
                                                                "
                                                                data-vv-as=" "
                                                                data-vv-validate-on="blur"
                                                                v-validate="
                                                                    'required|decimal:2|min_value:' +
                                                                    0 +
                                                                    '|max_value:' +
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .costo_neto_normal
                                                                "
                                                                class="mr-auto ml-auto input-cantidad"
                                                                maxlength="10"
                                                                v-model="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .costo_neto_descuento
                                                                "
                                                                :disabled="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .descuento_b ==
                                                                    0
                                                                "
                                                            />
                                                            <div
                                                                class="input-text"
                                                            >
                                                                <span>
                                                                    {{
                                                                        errors.first(
                                                                            "costo_neto_descuento_articulos_servicios" +
                                                                                indextr
                                                                        )
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </vs-td>
                                                        <vs-td v-else class="">
                                                            <div>N/A</div>
                                                        </vs-td>
                                                        <vs-td
                                                            class=""
                                                            v-if="
                                                                habilitar_plan_funerario_b ==
                                                                    false ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        1 &&
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ].plan_b ==
                                                                        0) ||
                                                                (habilitar_plan_funerario_b ==
                                                                    true &&
                                                                    form
                                                                        .plan_funerario_futuro_b
                                                                        .value ==
                                                                        0 &&
                                                                    form
                                                                        .plan_funerario_inmediato_b
                                                                        .value ==
                                                                        1)
                                                            "
                                                        >
                                                            <div
                                                                v-if="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .descuento_b ==
                                                                    1
                                                                "
                                                            >
                                                                $
                                                                {{
                                                                    (form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .costo_neto_descuento *
                                                                        form
                                                                            .articulos_servicios[
                                                                            indextr
                                                                        ]
                                                                            .cantidad)
                                                                        | numFormat(
                                                                            "0,000.00"
                                                                        )
                                                                }}
                                                            </div>
                                                            <div v-else>
                                                                $
                                                                {{
                                                                    (form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .costo_neto_normal *
                                                                        form
                                                                            .articulos_servicios[
                                                                            indextr
                                                                        ]
                                                                            .cantidad)
                                                                        | numFormat(
                                                                            "0,000.00"
                                                                        )
                                                                }}
                                                            </div>
                                                        </vs-td>
                                                        <vs-td v-else class="">
                                                            <div>$ 0.00</div>
                                                        </vs-td>
                                                        <vs-td
                                                            v-if="
                                                                habilitar_plan_funerario_b
                                                            "
                                                        >
                                                            <vs-switch
                                                                class="ml-auto mr-auto"
                                                                color="success"
                                                                icon-pack="feather"
                                                                v-model="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ].plan_b
                                                                "
                                                                :disabled="
                                                                    !habilitar_plan_funerario_b
                                                                "
                                                            >
                                                                <span slot="on"
                                                                    >SI</span
                                                                >
                                                                <span slot="off"
                                                                    >NO</span
                                                                >
                                                            </vs-switch>
                                                        </vs-td>
                                                        <vs-td v-else class="">
                                                            <div>N/A</div>
                                                        </vs-td>
                                                        <vs-td class="">
                                                            <vs-switch
                                                                class="ml-auto mr-auto"
                                                                color="success"
                                                                icon-pack="feather"
                                                                v-model="
                                                                    form
                                                                        .articulos_servicios[
                                                                        indextr
                                                                    ]
                                                                        .facturable_b
                                                                "
                                                            >
                                                                <span slot="on"
                                                                    >SI</span
                                                                >
                                                                <span slot="off"
                                                                    >NO</span
                                                                >
                                                            </vs-switch>
                                                        </vs-td>

                                                        <vs-td class="">
                                                            <div
                                                                class="flex justify-center"
                                                                @click="
                                                                    remover_articulo(
                                                                        indextr
                                                                    )
                                                                "
                                                                v-if="
                                                                    !fueCancelada
                                                                "
                                                            >
                                                                <img
                                                                    class="cursor-pointer img-btn-20 mx-3"
                                                                    src="@assets/images/minus.svg"
                                                                />
                                                            </div>
                                                        </vs-td>
                                                    </vs-tr>
                                                </template>
                                            </vs-table>
                                        </div>

                                        <div class="w-full">
                                            <div class="flex flex-wrap my-6">
                                                <div
                                                    class="w-full xl:w-8/12 px-2"
                                                >
                                                    <div class="flex flex-wrap">
                                                        <div
                                                            class="w-full input-text px-2"
                                                        >
                                                            <label
                                                                >Nota u
                                                                Observación:</label
                                                            >
                                                            <vs-textarea
                                                                height="240px"
                                                                :rows="9"
                                                                size="large"
                                                                ref="nota"
                                                                type="text"
                                                                class="w-full"
                                                                v-model.trim="
                                                                    form.nota
                                                                "
                                                            />
                                                        </div>
                                                    </div>
                                                    <!--fin del resumen de la venta-->
                                                </div>
                                                <div
                                                    class="w-full xl:w-4/12 px-2"
                                                >
                                                    <div class="flex flex-wrap">
                                                        <div
                                                            class="w-full input-text px-2 text-center"
                                                        >
                                                            <label>
                                                                Tasa IVA %
                                                                <span>(*)</span>
                                                            </label>
                                                            <vs-input
                                                                :disabled="
                                                                    tiene_pagos_realizados ||
                                                                    ventaLiquidada ||
                                                                    fueCancelada
                                                                "
                                                                size="large"
                                                                name="tasa_iva"
                                                                data-vv-as=" "
                                                                v-validate="
                                                                    'required|decimal:2|min_value:14|max_value:25'
                                                                "
                                                                type="text"
                                                                class="w-full cantidad"
                                                                placeholder="Porcentaje IVA"
                                                                v-model="
                                                                    form.tasa_iva
                                                                "
                                                                maxlength="2"
                                                            />
                                                            <span
                                                                class="mensaje-requerido"
                                                            >
                                                                {{
                                                                    errors.first(
                                                                        "tasa_iva"
                                                                    )
                                                                }}
                                                            </span>
                                                            <span
                                                                class="mensaje-requerido"
                                                                v-if="
                                                                    this.errores
                                                                        .tasa_iva
                                                                "
                                                                >{{
                                                                    errores
                                                                        .tasa_iva[0]
                                                                }}</span
                                                            >
                                                        </div>
                                                        <div
                                                            class="w-full px-2 text-center mt-3"
                                                            v-if="
                                                                verTotalUsoinmediato
                                                            "
                                                        >
                                                            <label
                                                                class="h4 font-medium color-copy"
                                                                >$ Total por
                                                                plan de uso
                                                                inmediato</label
                                                            >
                                                            <div
                                                                class="mt-1 pb-2 text-center"
                                                            >
                                                                <span
                                                                    class="h5"
                                                                >
                                                                    $
                                                                    {{
                                                                        totalUsoInmediato
                                                                            | numFormat(
                                                                                "0,000.00"
                                                                            )
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="w-full px-2 text-center mt-3"
                                                        >
                                                            <label
                                                                class="h4 font-medium color-copy"
                                                                >$ Total a
                                                                Pagar</label
                                                            >
                                                            <div
                                                                class="mt-3 text-center"
                                                            >
                                                                <span
                                                                    class="h5"
                                                                >
                                                                    $
                                                                    {{
                                                                        totalContrato
                                                                            | numFormat(
                                                                                "0,000.00"
                                                                            )
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="w-full px-2 size-base color-copy mt-3 text-center"
                                                        >
                                                            <span
                                                                class="color-danger-900 font-medium"
                                                                >Ojo:</span
                                                            >
                                                            Los costos de los
                                                            conceptos capturados
                                                            ya incluyen el IVA.
                                                        </div>

                                                        <div
                                                            class="w-full input-text px-2"
                                                        >
                                                            <vs-button
                                                                v-if="
                                                                    !fueCancelada
                                                                "
                                                                class="w-full ml-auto mr-auto mt-3"
                                                                @click="
                                                                    acceptAlert()
                                                                "
                                                                color="success"
                                                            >
                                                                <span
                                                                    >Guardar
                                                                    Contrato</span
                                                                >
                                                            </vs-button>
                                                        </div>
                                                        <!--fin de precios-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--Fin Conceptos-->
                        </div>
                    </div>
                    <!--Fin Contrato-->
                </div>
            </div>
            <!--fin venta-->
        </vs-popup>
        <Password
            v-if="openPassword"
            :z_index="'z-index59k'"
            :show="openPassword"
            :callback-on-success="callback"
            @closeVerificar="closePassword"
            :accion="accionNombre"
        ></Password>
        <ConfirmarDanger
            v-if="openConfirmarSinPassword"
            :z_index="'z-index59k'"
            :show="openConfirmarSinPassword"
            :callback-on-success="callBackConfirmar"
            @closeVerificar="openConfirmarSinPassword = false"
            :accion="accionConfirmarSinPassword"
            :confirmarButton="botonConfirmarSinPassword"
        ></ConfirmarDanger>
        <ConfirmarAceptar
            v-if="openConfirmarAceptar"
            :z_index="'z-index59k'"
            :show="openConfirmarAceptar"
            :callback-on-success="callBackConfirmarAceptar"
            @closeVerificar="openConfirmarAceptar = false"
            :accion="'He revisado la información y quiero guardar la venta'"
            :confirmarButton="'Guardar Venta'"
        >
        </ConfirmarAceptar>
        <ClientesBuscador
            v-if="openBuscador"
            :z_index="'z-index55k'"
            :show="openBuscador"
            @closeBuscador="openBuscador = false"
            @retornoCliente="clienteSeleccionado"
        ></ClientesBuscador>
        <ArticulosBuscador
            v-if="openBuscadorArticulos"
            :z_index="'z-index56k'"
            :show="openBuscadorArticulos"
            @closeBuscador="openBuscadorArticulos = false"
            @LoteSeleccionado="LoteSeleccionado"
        ></ArticulosBuscador>
        <TerrenosBuscador
            v-if="openBuscadorTerreno"
            :z_index="'z-index55k'"
            :show="openBuscadorTerreno"
            @closeBuscador="openBuscadorTerreno = false"
            @retornoTerreno="TerrenoSeleccionado"
        ></TerrenosBuscador>
        <PlanesBuscador
            v-if="openBuscadorPlan"
            :z_index="'z-index55k'"
            :show="openBuscadorPlan"
            @closeBuscador="openBuscadorPlan = false"
            @retornoPlan="PlanSeleccionado"
        ></PlanesBuscador>
    </div>
</template>
<script>
/**date picker */
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import "flatpickr/dist/themes/airbnb.css";
import ConfirmarDanger from "@pages/ConfirmarDanger";
//componente de password
import Password from "@pages/confirmar_password";
import funeraria from "@services/funeraria";
import vSelect from "vue-select";
import ConfirmarAceptar from "@pages/confirmarAceptar.vue";
import ClientesBuscador from "@pages/clientes/searcher.vue";
import TerrenosBuscador from "@pages/cementerio/searcher.vue";
import PlanesBuscador from "@pages/funeraria/ventas/searcher.vue";
import ArticulosBuscador from "@pages/funeraria/servicios_funerarios/searcher_articulos.vue";

import clientes from "@services/clientes";

/**VARIABLES GLOBALES */
import {
    alfabeto,
    configdateTimePicker,
    configdateTimePickerWithTime,
} from "@/VariablesGlobales";
import { tr } from "date-fns/locale";

export default {
    name: "FormularioServiciosFunerarios",
    components: {
        "v-select": vSelect,
        flatPickr,
        Password,
        ConfirmarDanger,
        ConfirmarAceptar,
        ClientesBuscador,
        TerrenosBuscador,
        PlanesBuscador,
        ArticulosBuscador,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        //para saber que tipo de formulario es
        tipo: {
            type: String,
            required: true,
        },
        id_solicitud: {
            type: Number,
            required: false,
            default: 0,
        },
        z_index: {
            type: String,
            required: false,
            default: "z-index54k",
        },
    },
    watch: {
        show: {
            immediate: true, // runs when component is mounted too
            async handler(newValue) {
                if (newValue) {
                    await this.get_nacionalidades();
                    await this.get_estados_afectado();
                    await this.get_titulos();
                    await this.get_estados_civiles();
                    await this.get_escolaridades();
                    await this.get_afiliaciones();
                    await this.get_sitios_muerte();
                    await this.get_lugares_velacion();
                    await this.get_lugares_inhumacion();
                    await this.get_material_velacion();
                    await this.get_tipos_contratante();
                    await this.get_legistas_embalsamadores();
                    await this.get_solicitudes_servicios_id();
                    /**aqui cargo la informacion que existe hasta el momento sobre este servicio */
                    this.$popupManager.register(
                        this,
                        this.cancelar,
                        "fallecido_ref"
                    );
                } else {
                    this.$popupManager.unregister(this.$options.name);
                }
                this.localShow = newValue;
            },
        },
        "form.inhumacion_b": function (newValue, oldValue) {
            ///this.form.ubicacion = "";
            //this.form.ventas_terrenos_id = "";
        },
        "form.cementerio_servicio": function (newValue, oldValue) {
            //this.form.ubicacion_convenio = "";
            //this.form.ventas_terrenos_id = "";
        },

        "form.plan_funerario_futuro_b": function (newValue, oldValue) {
            this.secciones = [];
            if (newValue.value == 0) {
                (async () => {
                    await this.get_planes_uso_inmediato();
                })();
                /*this.form.plan = "";
                this.form.id_convenio_plan = "";
                this.form.tipo_contratante = {
                  value: "",
                  label: "Seleccione 1",
                };*/
            } else {
                if (this.secciones_original == []) {
                    this.secciones =
                        this.datosPlanFunerario.venta_plan.secciones_original;
                } else {
                    this.secciones = this.secciones_original;
                }
                /**cargar datos de origen del plan de uso a futuro */
            }
        },
        "form.plan_funerario_inmediato_b": function (newValue, oldValue) {
            if (newValue.value == 1) {
                if (this.data_contrato != []) {
                    /**el contrato tiene un plan funerario de uso inmediato */
                    //se selecciona este por defecto
                    this.form.plan_funerario =
                        this.planes_funerarios[
                            this.planes_funerarios.length - 1
                        ];
                }
                this.secciones = this.form.plan_funerario.secciones;
            } else {
                this.form.plan_funerario = this.planes_funerarios[0];
            }
        },
        "form.plan_funerario": function (newValue, oldValue) {
            if (newValue.value != "") {
                this.secciones = this.form.plan_funerario.secciones;
            } else {
                this.secciones = [];
            }
        },
        /**aqui estoy */
        "form.id_convenio_plan": function (newValue, oldValue) {
            if (newValue != "" && newValue > 0) {
                /**buscar servicios que hayan usado este plan funerario */
                this.$vs.loading();
                (async () => {
                    await funeraria
                        .get_solicitudes_servicios_id_uso_plan_funerario_futuro(
                            newValue
                        )
                        .then((res) => {
                            if (res.data.length > 0) {
                                this.contratos_con_uso_plan_funerario_futuro_seleccionado =
                                    res.data;
                            } else {
                                this.contratos_con_uso_plan_funerario_futuro_seleccionado =
                                    [];
                                /**no hay datos que mostrar*/
                            }
                            /**aqui cargo los datos obtenidos */
                            this.$vs.loading.close();
                        })
                        .catch((err) => {
                            this.contratos_con_uso_plan_funerario_futuro_seleccionado =
                                [];
                            this.$vs.loading.close();
                        });
                })();
            } else {
                /**limpiar los datos */
            }
        },
        "form.ventas_terrenos_id": function (newValue, oldValue) {
            if (newValue != "" && newValue > 0) {
                /**buscar servicios que hayan usado este terreno */
                this.$vs.loading();
                (async () => {
                    await funeraria
                        .get_solicitudes_servicios_id_uso_terreno(newValue)
                        .then((res) => {
                            if (res.data.length > 0) {
                                this.contratos_con_uso_terreno_seleccionado =
                                    res.data;
                            } else {
                                this.contratos_con_uso_terreno_seleccionado =
                                    [];
                                /**no hay datos que mostrar*/
                            }
                            /**aqui cargo los datos obtenidos */
                            this.$vs.loading.close();
                        })
                        .catch((err) => {
                            this.contratos_con_uso_terreno_seleccionado = [];
                            this.$vs.loading.close();
                        });
                })();
            } else {
                /**limpiar los datos */
            }
        },
    },
    computed: {
        esExhumacion: function () {
            if (
                this.getTipoformulario == "exhumar" ||
                this.getTipoformulario == "modificar_exhumar"
            ) {
                return true;
            } else {
                return false;
            }
        },
        verUsoConvenios: function () {
            if (
                this.form.inhumacion_b == 0 &&
                this.form.plan_funerario_futuro_b.value == 0
            ) {
                return false;
            } else {
                if (
                    (this.form.id_convenio_plan > 0 &&
                        this.form.id_convenio_plan != "") ||
                    (this.form.ventas_terrenos_id != 0 &&
                        this.form.ventas_terrenos_id != "")
                ) {
                    return true;
                } else {
                    return false;
                }
            }
        },

        /**validaciones de los selects */
        nacionalidad_validacion_computed: function () {
            return this.form.nacionalidad.value;
        },

        estado_civil_validacion_computed: function () {
            return this.form.estado_civil.value;
        },

        escolaridad_validacion_computed: function () {
            return this.form.escolaridad.value;
        },

        afiliacion_validacion_computed: function () {
            return this.form.afiliacion.value;
        },

        sitio_muerte_validacion_computed: function () {
            return this.form.sitio_muerte.value;
        },

        estado_cuerpo_validacion_computed: function () {
            return this.form.estado_cuerpo.value;
        },
        legista_validacion_computed: function () {
            return this.form.legista.value;
        },
        embalsamador_validacion_computed: function () {
            return this.form.embsalsamador.value;
        },
        genero_validacion_computed: function () {
            return this.form.genero.value;
        },

        preparador_validacion_computed: function () {
            if (this.form.embalsamar_b == 1) {
                return this.form.preparador;
            } else {
                return true;
            }
        },

        lugar_servicio_validacion_computed: function () {
            if (this.form.velacion_b == 1) {
                return this.form.lugar_servicio.value;
            } else {
                return true;
            }
        },

        direccion_velacion_validacion_computed: function () {
            if (this.form.velacion_b == 1) {
                return this.form.direccion_velacion;
            } else {
                return true;
            }
        },

        fechahora_cremacion_validacion_computed: function () {
            if (this.form.cremacion_b == 1) {
                return this.form.fechahora_cremacion;
            } else {
                return true;
            }
        },

        fechahora_entrega_cenizas_validacion_computed: function () {
            if (this.form.cremacion_b == 1) {
                return this.form.fechahora_entrega_cenizas;
            } else {
                return true;
            }
        },

        cementerio_servicio_validacion_computed: function () {
            if (this.form.inhumacion_b == 1) {
                return this.form.cementerio_servicio.value;
            } else {
                return true;
            }
        },

        fechahora_inhumacion_validacion_computed: function () {
            if (this.form.inhumacion_b == 1) {
                return this.form.fechahora_inhumacion;
            } else {
                return true;
            }
        },

        ubicacion_validacion_computed: function () {
            if (
                this.form.inhumacion_b == 1 &&
                this.form.cementerio_servicio.value > 1
            ) {
                return this.form.ubicacion;
            } else {
                return true;
            }
        },

        ventas_terrenos_id_validacion_computed: function () {
            if (
                this.form.inhumacion_b == 1 &&
                this.form.cementerio_servicio.value == 1
            ) {
                return this.form.ventas_terrenos_id;
            } else {
                return true;
            }
        },

        fechahora_traslado_validacion_computed: function () {
            if (this.form.traslado_b == 1) {
                return this.form.fechahora_traslado;
            } else {
                return true;
            }
        },

        destino_traslado_validacion_computed: function () {
            if (this.form.traslado_b == 1) {
                return this.form.destino_traslado;
            } else {
                return true;
            }
        },

        aseguradora_validacion_computed: function () {
            if (this.form.aseguradora_b == 1) {
                return this.form.aseguradora;
            } else {
                return true;
            }
        },

        fechahora_misa_validacion_computed: function () {
            if (this.form.misa_b == 1) {
                return this.form.fechahora_misa;
            } else {
                return true;
            }
        },

        iglesia_misa_validacion_computed: function () {
            if (this.form.misa_b == 1) {
                return this.form.iglesia_misa;
            } else {
                return true;
            }
        },

        folio_acta_validacion_computed: function () {
            if (this.form.acta_b == 1) {
                return this.form.folio_acta;
            } else {
                return true;
            }
        },

        fecha_acta_validacion_computed: function () {
            if (this.form.acta_b == 1) {
                return this.form.fecha_acta;
            } else {
                return true;
            }
        },

        tipo_contrato_validacion_computed: function () {
            return this.form.tipo_contrato.value;
        },

        titulo_validacion_computed: function () {
            return this.form.titulo.value;
        },

        fecha_nacimiento_validacion_computed: function () {
            return this.form.fecha_nacimiento;
        },

        fechahora_defuncion_validacion_computed: function () {
            return this.form.fechahora_defuncion;
        },

        fechahora_contrato_validacion_computed: function () {
            return this.form.fechahora_contrato;
        },

        id_convenio_plan_validacion_computed: function () {
            if (this.form.plan_funerario_futuro_b.value == 1) {
                return this.form.id_convenio_plan;
            } else {
                return true;
            }
        },

        tipo_contratante_validacion_computed: function () {
            if (this.form.plan_funerario_futuro_b.value == 1) {
                return this.form.tipo_contratante.value;
            } else {
                return true;
            }
        },

        plan_funerario_validacion_computed: function () {
            if (this.form.plan_funerario_inmediato_b.value == 1) {
                return this.form.plan_funerario.value;
            } else {
                return true;
            }
        },

        /**costo del plan a uso inmediato */
        costo_uso_inmediato_computed: function () {
            if (this.form.plan_funerario.value != "") {
                return this.form.plan_funerario.costo_neto;
            } else {
                return 0;
            }
        },

        /**controla si deja al usuario decidir si aplicar al plan funerario o no */
        habilitar_plan_funerario_b: function () {
            if (this.form.plan_funerario_futuro_b.value == 1) {
                return true;
                /* if (
                  this.form.id_convenio_plan <= 0 ||
                  this.form.id_convenio_plan == ""
                ) {
                  return false;
                } else {
                  return true;
                }*/
            } else {
                if (this.form.plan_funerario_inmediato_b.value != 1) {
                    return false;
                } else {
                    return true;
                    /*if (this.form.plan_funerario.value == "") {
                      return false;
                    } else {
                      return true;
                    }*/
                }
            }
        },
        verTotalUsoinmediato: function () {
            if (
                this.habilitar_plan_funerario_b == true &&
                this.form.plan_funerario_futuro_b.value == 0 &&
                this.form.plan_funerario_inmediato_b.value == 1
            ) {
                return true;
            } else {
                return false;
            }
        },
        totalUsoInmediato: function () {
            let total = 0;
            this.form.articulos_servicios.forEach((element, index) => {
                /**calculo tomando en cuenta que tiene un plan de uso inmdiato */
                if (
                    this.habilitar_plan_funerario_b == true &&
                    this.form.plan_funerario_futuro_b.value == 0 &&
                    this.form.plan_funerario_inmediato_b.value == 1
                ) {
                    if (this.form.articulos_servicios[index].plan_b == 1) {
                        if (
                            this.form.articulos_servicios[index].descuento_b ==
                            1
                        ) {
                            total +=
                                this.form.articulos_servicios[index].cantidad *
                                this.form.articulos_servicios[index]
                                    .costo_neto_descuento;
                        } else {
                            total +=
                                this.form.articulos_servicios[index].cantidad *
                                this.form.articulos_servicios[index]
                                    .costo_neto_normal;
                        }
                    }
                }
            });
            return total;
        },
        totalContrato: function () {
            let total = 0;
            this.form.articulos_servicios.forEach((element, index) => {
                /**calculo tomando en cuenta que no tiene seleccionado un plan fuenerario a futuro */
                if (
                    this.habilitar_plan_funerario_b == false ||
                    (this.habilitar_plan_funerario_b == true &&
                        this.form.plan_funerario_futuro_b.value == 0 &&
                        this.form.plan_funerario_inmediato_b.value == 1)
                ) {
                    if (this.form.articulos_servicios[index].descuento_b == 1) {
                        total +=
                            this.form.articulos_servicios[index].cantidad *
                            this.form.articulos_servicios[index]
                                .costo_neto_descuento;
                    } else {
                        total +=
                            this.form.articulos_servicios[index].cantidad *
                            this.form.articulos_servicios[index]
                                .costo_neto_normal;
                    }
                } else {
                    /**calculo tomado en cuenta que tiene seleccionado un plan funerario a futuro */
                    if (this.form.articulos_servicios[index].plan_b != 1) {
                        /**tomando en cuenta que el concepto no es parte del plan */

                        /**no siendo parte del plan */
                        if (
                            this.form.articulos_servicios[index].descuento_b ==
                            1
                        ) {
                            total +=
                                this.form.articulos_servicios[index].cantidad *
                                this.form.articulos_servicios[index]
                                    .costo_neto_descuento;
                        } else {
                            total +=
                                this.form.articulos_servicios[index].cantidad *
                                this.form.articulos_servicios[index]
                                    .costo_neto_normal;
                        }
                    }
                }
            });
            return total;
        },
        getTipoformulario: {
            get() {
                return this.tipo;
            },
            set(newValue) {
                return newValue;
            },
        },
        get_id_solicitud: {
            get() {
                return this.id_solicitud;
            },
            set(newValue) {
                return newValue;
            },
        },

        verLista: function () {
            if (this.secciones.length > 0) {
                let mostrar = false;
                this.conceptos = [];
                this.secciones.forEach((element, index_seccion) => {
                    if (element.conceptos) {
                        if (element.conceptos.length > 0) {
                            element.conceptos.forEach(
                                (concepto, index_concepto) => {
                                    this.conceptos.push({
                                        concepto: concepto.concepto,
                                        concepto_ingles:
                                            concepto.concepto_ingles,
                                        aplicar: concepto.aplicar_en,
                                    });
                                }
                            );
                            mostrar = true;
                        }
                    }
                });
                if (mostrar == true) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        },
    },
    data() {
        return {
            localShow: false,
            contratos_con_uso_terreno_seleccionado: [],
            contratos_con_uso_plan_funerario_futuro_seleccionado: [],
            saldo_neto_terreno: 0,
            datosPlanFunerario: [],
            secciones_original: [],
            secciones: [],
            activeTab: 0,
            generos: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
                {
                    value: 1,
                    label: "Hombre",
                },
                {
                    value: 2,
                    label: "Mujer",
                },
            ],
            nacionalidades: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            legistas: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            embsalsamadores: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            titulos: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            estados_civiles: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            afiliaciones: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            escolaridades: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            sino: [
                {
                    value: "1",
                    label: "SI",
                },
                {
                    value: "0",
                    label: "NO",
                },
            ],
            sitios_muerte: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            estados_cuerpo: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            lugares_servicio: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            cementerios_servicio: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            planes_funerarios: [
                {
                    value: "",
                    label: "Seleccione 1",
                    secciones: [],
                    costo_neto: 0,
                },
            ],
            tipos_contratante: [
                {
                    value: "",
                    label: "Seleccione 1",
                },
            ],
            serverOptions: {
                numero_control: "",
            },
            form: {
                index_articulo_servicio: "",
                /**DATOS DEL CONTRATO DE LA BD */
                data_contrato: [],
                /**ID DEL SERVICIO */
                id_servicio: 0,
                /**fallecido */
                nombre_afectado: "",
                fecha_nacimiento: "",
                edad: "",
                genero: {
                    value: "",
                    label: "Seleccione 1",
                },
                titulo: {
                    value: "",
                    label: "Seleccione 1",
                },
                nacionalidad: {
                    value: "",
                    label: "Seleccione 1",
                },
                legista: {
                    value: "",
                    label: "Seleccione 1",
                },
                embalsamador: {
                    value: "",
                    label: "Seleccione 1",
                },
                lugar_nacimiento: "",
                ocupacion: "",
                direccion_fallecido: "",
                estado_civil: {
                    value: "",
                    label: "Seleccione 1",
                },
                afiliacion: {
                    value: "",
                    label: "Seleccione 1",
                },
                escolaridad: {
                    value: "",
                    label: "Seleccione 1",
                },
                afiliacion_nota: "",
                /**fin datos fallecido */

                /**certificad de defuncion */
                folio_certificado: "",
                fechahora_defuncion: "",
                causa_muerte: "",
                muerte_natural_b: {
                    value: "1",
                    label: "SI",
                },
                sitio_muerte: {
                    value: "",
                    label: "Seleccione 1",
                },
                lugar_muerte: "",
                atencion_medica_b: {
                    value: "1",
                    label: "SI",
                },
                contagioso_b: {
                    value: "1",
                    label: "SI",
                },
                enfermedades_padecidas: "",
                certificado_informante: "",
                certificado_informante_telefono: "",
                certificado_informante_parentesco: "",
                medico_legista: "",
                cedula_legista: "",
                estado_cuerpo: {
                    value: "",
                    label: "Seleccione 1",
                },
                /**fin de datos dle certificado */

                /**datos del destino del servicio */
                embalsamar_b: 0,
                medico_responsable_embalsamado: "",
                preparador: "",
                cedula_embalsamador: "",
                velacion_b: 0,
                lugar_servicio: {
                    value: "",
                    label: "Seleccione 1",
                },
                direccion_velacion: "",
                cremacion_b: 0,
                fechahora_cremacion: "",
                fechahora_entrega_cenizas: "",
                descripcion_urna: "",
                inhumacion_b: 0,
                cementerio_servicio: {
                    value: "",
                    label: "Seleccione 1",
                },
                fechahora_inhumacion: "",
                ubicacion: "",
                ubicacion_convenio: "",
                ventas_terrenos_id: 0,
                traslado_b: 0,
                fechahora_traslado: "",
                destino_traslado: "",
                aseguradora_b: 0,
                numero_convenio_aseguradora: "",
                aseguradora: "",
                telefono_aseguradora: "",
                misa_b: 0,
                fechahora_misa: "",
                iglesia_misa: "",
                direccion_iglesia: "",
                custodia_b: 0,
                responsable_custodia: "",
                folio_custodia: "",
                folio_liberacion: "",
                /**fin de datos del destino del servicio */

                /**equipo de velacion */
                material_velacion_b: 1,
                material_velacion: [],
                /**fin de equipo de velacion */

                /**datos del acta de defuncion */
                acta_b: 1,
                folio_acta: "",
                fecha_acta: "",
                /**fin datos del acta de defuncion */
                /**datos del contrato */
                tipo_contratante: {
                    value: "",
                    label: "Seleccione 1",
                },

                fechahora_contrato: "",
                id_cliente: "",
                cliente: "",
                direccion_cliente: "",
                parentesco_contratante: "",

                plan_funerario_futuro_b: {
                    value: "1",
                    label: "SI",
                },

                id_convenio_plan: "",
                plan: "",
                plan_funerario_inmediato_b: {
                    value: "1",
                    label: "SI",
                },
                plan_funerario: {
                    value: "",
                    label: "Seleccione 1",
                    plan: "",
                    secciones: [],
                    costo_neto: 0,
                },
                articulos_servicios: [],
                /**fin datos del contrato */
                tasa_iva: 16,
                nota: "",
            },
            /**variables dle modulo */
            openBuscadorArticulos: false,
            openBuscadorTerreno: false,
            openBuscadorPlan: false,
            openNotas: false,
            configdateTimePicker: configdateTimePicker,
            configdateTimePickerWithTime: configdateTimePickerWithTime,
            verDisponibilidad: false,
            openBuscador: false,
            accionConfirmarSinPassword: "",
            botonConfirmarSinPassword: "",
            alfabeto: alfabeto,
            openPassword: false,
            openConfirmarSinPassword: false,
            callback: Function,
            callBackConfirmar: Function,
            openConfirmarAceptar: false,
            callBackConfirmarAceptar: Function,
            accionNombre: "Actualizar Contrato",
            errores: [],
        };
    },
    methods: {
        async get_solicitudes_servicios_id() {
            this.$vs.loading();
            await funeraria
                .get_solicitudes_servicios_id(this.get_id_solicitud)
                .then((res) => {
                    if (res.data.length > 0) {
                        /**hay datos que mostrar */
                        let data = res.data[0];
                        this.data_contrato = data;
                        /**cargando el tipo de titulo que tiene la persona */
                        for (
                            let index = 0;
                            index < this.titulos.length;
                            index++
                        ) {
                            if (this.titulos[index].value == data.titulos_id) {
                                this.form.titulo = this.titulos[index];
                                break;
                            }
                        }
                        /**nombre del afectado */
                        this.form.nombre_afectado = data.nombre_afectado;
                        var fecha_nacimiento =
                            data.fecha_nacimiento != null
                                ? data.fecha_nacimiento.split("-")
                                : null;
                        //yyyy-mm-dd
                        this.form.fecha_nacimiento =
                            data.fecha_nacimiento != null
                                ? new Date(
                                      fecha_nacimiento[0],
                                      fecha_nacimiento[1] - 1,
                                      fecha_nacimiento[2]
                                  )
                                : null;
                        /**cargando el genero */
                        this.generos.forEach((genero) => {
                            if (genero.value == data.generos_id) {
                                this.form.genero = genero;
                                return;
                            }
                        });
                        /**cargando la nacionalidad */
                        if (data.nacionalidades_id > 0) {
                            this.form.nacionalidad = {
                                value: data.nacionalidad.id,
                                label: data.nacionalidad.nacionalidad,
                            };
                        }

                        this.form.lugar_nacimiento = data.lugar_nacimiento;
                        this.form.ocupacion = data.ocupacion;
                        this.form.direccion_fallecido =
                            data.direccion_fallecido;

                        /**cargando el estado civil del fallecido */
                        this.estados_civiles.forEach((element) => {
                            if (element.value == data.estados_civiles_id) {
                                this.form.estado_civil = element;
                                return;
                            }
                        });

                        /**cargando la escolaridad del fallecido */
                        this.escolaridades.forEach((element) => {
                            if (element.value == data.escolaridades_id) {
                                this.form.escolaridad = element;
                                return;
                            }
                        });

                        /**cargando la afiliacion del fallecido */
                        this.afiliaciones.forEach((element) => {
                            if (element.value == data.afiliaciones_id) {
                                this.form.afiliacion = element;
                                return;
                            }
                        });
                        this.form.afiliacion_nota = data.afiliacion_nota;

                        /**comienza a llenar los datos relavantes al certificado medico */
                        this.form.folio_certificado = data.folio_certificado;
                        if (data.fechahora_defuncion != null) {
                            var fecha_defuncion = data.fecha_muerte.split("-");
                            var hora_defuncion = data.hora_muerte.split(":");
                            //yyyy-mm-dd hh:mm
                            this.form.fechahora_defuncion = new Date(
                                fecha_defuncion[0],
                                fecha_defuncion[1] - 1,
                                fecha_defuncion[2],
                                hora_defuncion[0],
                                hora_defuncion[1]
                            );
                        }
                        this.form.causa_muerte = data.causa_muerte;
                        this.sino.forEach((element) => {
                            if (element.value == data.muerte_natural_b) {
                                this.form.muerte_natural_b = element;
                                return;
                            }
                        });
                        this.sino.forEach((element) => {
                            if (element.value == data.contagioso_b) {
                                this.form.contagioso_b = element;
                                return;
                            }
                        });

                        /**sitio de muerte */
                        this.sitios_muerte.forEach((element) => {
                            if (element.value == data.sitios_muerte_id) {
                                this.form.sitio_muerte = element;
                                return false;
                            }
                        });
                        this.form.lugar_muerte = data.lugar_muerte;
                        this.sino.forEach((element) => {
                            if (element.value == data.atencion_medica_b) {
                                this.form.atencion_medica_b = element;
                                return;
                            }
                        });
                        this.form.enfermedades_padecidas =
                            data.enfermedades_padecidas;
                        this.form.certificado_informante =
                            data.certificado_informante;
                        this.form.certificado_informante_telefono =
                            data.certificado_informante_telefono;
                        this.form.certificado_informante_parentesco =
                            data.certificado_informante_parentesco;

                        /**Legistas */
                        this.legistas.forEach((element) => {
                            if (element.value == data.legista_id) {
                                this.form.legista = element;
                                return false;
                            }
                        });
                        this.form.medico_legista = data.medico_legista;
                        this.form.cedula_legista = data.cedula_legista;
                        this.estados_cuerpo.forEach((element) => {
                            if (this.esExhumacion) {
                                if (element.value == 5) {
                                    this.form.estado_cuerpo = element;
                                    return;
                                }
                            } else {
                                if (element.value == data.estado_afectado_id) {
                                    this.form.estado_cuerpo = element;
                                    return;
                                }
                            }
                        });

                        /**comenzando datos de destinos del servicio */
                        /**Preparadores */
                        this.form.embalsamar_b = data.embalsamar_b;
                        if (this.form.embalsamar_b == 1) {
                            this.embsalsamadores.forEach((element) => {
                                if (element.value == data.embalsamador_id) {
                                    this.form.embalsamador = element;
                                    return false;
                                }
                            });
                            this.form.medico_responsable_embalsamado =
                                data.medico_responsable_embalsamado;
                            this.form.preparador = data.preparador;
                            this.form.cedula_embalsamador =
                                data.cedula_embalsamador;
                        } else {
                            this.form.embalsamar_b = 0;
                            this.form.embalsamador = this.embsalsamadores[0];
                        }

                        this.form.cremacion_b = data.cremacion_b;
                        if (
                            this.form.cremacion_b == 1 &&
                            data.fechahora_cremacion != null
                        ) {
                            var fecha_cremacion =
                                data.fecha_cremacion.split("-");
                            var hora_cremacion = data.hora_cremacion.split(":");
                            //yyyy-mm-dd hh:mm
                            this.form.fechahora_cremacion = new Date(
                                fecha_cremacion[0],
                                fecha_cremacion[1] - 1,
                                fecha_cremacion[2],
                                hora_cremacion[0],
                                hora_cremacion[1]
                            );
                        } else {
                            this.form.cremacion_b = 0;
                            this.form.fechahora_cremacion = null;
                        }

                        if (
                            this.form.cremacion_b == 1 &&
                            data.fechahora_entrega_cenizas != null
                        ) {
                            var fecha_cremacion =
                                data.fecha_entrega_cenizas.split("-");
                            var hora_cremacion =
                                data.hora_entrega_cenizas.split(":");
                            //yyyy-mm-dd hh:mm
                            this.form.fechahora_entrega_cenizas = new Date(
                                fecha_cremacion[0],
                                fecha_cremacion[1] - 1,
                                fecha_cremacion[2],
                                hora_cremacion[0],
                                hora_cremacion[1]
                            );
                        } else {
                            this.form.fechahora_entrega_cenizas = null;
                        }

                        if (this.form.cremacion_b == 1) {
                            this.form.descripcion_urna = data.descripcion_urna;
                        }

                        this.form.traslado_b = data.traslado_b;
                        if (
                            this.form.traslado_b == 1 &&
                            data.fechahora_traslado != null
                        ) {
                            var fecha_traslado = data.fecha_traslado.split("-");
                            var hora_traslado = data.hora_traslado.split(":");
                            //yyyy-mm-dd hh:mm
                            this.form.fechahora_traslado = new Date(
                                fecha_traslado[0],
                                fecha_traslado[1] - 1,
                                fecha_traslado[2],
                                hora_traslado[0],
                                hora_traslado[1]
                            );
                        } else {
                            this.form.traslado_b = 0;
                        }

                        if (this.form.traslado_b == 1) {
                            this.form.destino_traslado = data.destino_traslado;
                        }

                        this.form.misa_b = data.misa_b;

                        if (
                            this.form.misa_b == 1 &&
                            data.fechahora_misa != null
                        ) {
                            var fecha_misa = data.fecha_misa.split("-");
                            var hora_misa = data.hora_misa.split(":");
                            //yyyy-mm-dd hh:mm
                            this.form.fechahora_misa = new Date(
                                fecha_misa[0],
                                fecha_misa[1] - 1,
                                fecha_misa[2],
                                hora_misa[0],
                                hora_misa[1]
                            );
                        } else {
                            this.form.misa_b = 0;
                        }

                        if (this.form.misa_b == 1) {
                            this.form.iglesia_misa = data.iglesia_misa;
                            this.form.direccion_iglesia =
                                data.direccion_iglesia;
                        }

                        /**velacion */
                        this.form.velacion_b = data.velacion_b;
                        if (this.form.velacion_b == 1) {
                            this.lugares_servicio.forEach((element) => {
                                if (
                                    element.value == data.lugares_servicios_id
                                ) {
                                    this.form.lugar_servicio = element;
                                    return;
                                }
                            });
                            this.form.direccion_velacion =
                                data.direccion_velacion;
                        } else {
                            this.form.velacion_b = 0;
                        }
                        /**fin de velacion */

                        /**datos para la inhumacion del cuerpo */
                        this.form.inhumacion_b = data.inhumacion_b;
                        if (this.form.inhumacion_b == 1) {
                            this.cementerios_servicio.forEach((element) => {
                                if (
                                    element.value ==
                                    data.cementerios_servicio_id
                                ) {
                                    this.form.cementerio_servicio = element;
                                    return;
                                }
                            });
                            if (this.form.inhumacion_b == 1) {
                                if (data.fechahora_inhumacion != null) {
                                    var fecha_inhumacion =
                                        data.fecha_inhumacion.split("-");
                                    var hora_inhumacion =
                                        data.hora_inhumacion.split(":");
                                    //yyyy-mm-dd hh:mm
                                    this.form.fechahora_inhumacion = new Date(
                                        fecha_inhumacion[0],
                                        fecha_inhumacion[1] - 1,
                                        fecha_inhumacion[2],
                                        hora_inhumacion[0],
                                        hora_inhumacion[1]
                                    );
                                }
                            }
                            if (data.cementerios_servicio_id == 1) {
                                /**es de cementerio aeternus */
                                this.form.ubicacion_convenio =
                                    data.terreno.ubicacion_servicio;
                                this.form.ventas_terrenos_id =
                                    data.terreno.ventas_terrenos_id;
                                this.saldo_neto_terreno =
                                    data.terreno.saldo_neto;
                            } else {
                                /**es de cualquier otro cementerio */
                                this.form.ubicacion = data.nota_ubicacion;
                            }
                        } else {
                            this.form.inhumacion_b = 0;
                        }

                        /**datos de la aseguradora */
                        this.form.aseguradora_b = data.aseguradora_b;
                        if (data.aseguradora_b == 1) {
                            /**datos de la aseguradora que existen en la bd */
                            this.form.numero_convenio_aseguradora =
                                data.numero_convenio_aseguradora;
                            this.form.aseguradora = data.aseguradora;
                            this.form.telefono_aseguradora =
                                data.telefono_aseguradora;
                        } else {
                            this.form.aseguradora_b = 0;
                        }

                        /**datos de la cadena de custodia */
                        this.form.custodia_b = data.custodia_b;
                        if (data.custodia_b == 1) {
                            /**datos de la cadena de custodia que existen en la bd */
                            this.form.responsable_custodia =
                                data.responsable_custodia;
                            this.form.folio_custodia = data.folio_custodia;
                            this.form.folio_liberacion = data.folio_liberacion;
                        } else {
                            this.form.custodia_b = 0;
                        }

                        /**datos del material de velacion */
                        this.form.material_velacion_b =
                            data.material_velacion_b;
                        if (data.material_velacion_b == 1) {
                            /**cargando el material de velacion que tiene este contrato*/
                            this.form.material_velacion.forEach((material) => {
                                data.materialrentado.forEach((rentado) => {
                                    if (material.id == rentado.articulos_id) {
                                        material.cantidad = rentado.cantidad;
                                        material.nota = rentado.nota;
                                        return;
                                    }
                                });
                            });
                        } else {
                            this.form.material_velacion_b = 0;
                        }

                        /**datos del acta */
                        this.form.acta_b = data.acta_b;
                        if (data.acta_b == 1) {
                            /**datos de la cadena de custodia que existen en la bd */
                            this.form.folio_acta = data.folio_acta;
                            if (data.fecha_acta != null) {
                                var fecha_acta = data.fecha_acta.split("-");
                                //yyyy-mm-dd hh:mm
                                this.form.fecha_acta = new Date(
                                    fecha_acta[0],
                                    fecha_acta[1] - 1,
                                    fecha_acta[2]
                                );
                            }
                            this.form.folio_acta = data.folio_acta;
                        } else {
                            this.form.acta_b = 0;
                        }

                        /**datos del tab de contrato */

                        if (data.fechahora_contrato != null) {
                            var fecha_contrato = data.fecha_contrato.split("-");
                            var hora_contrato = data.hora_contrato.split(":");
                            //yyyy-mm-dd hh:mm
                            this.form.fechahora_contrato = new Date(
                                fecha_contrato[0],
                                fecha_contrato[1] - 1,
                                fecha_contrato[2],
                                hora_contrato[0],
                                hora_contrato[1]
                            );
                        }
                        /**aqui revisamos si el contrato ya fue asigando a una operacion */
                        if (data.operacion != null) {
                            /**al si tener registrada una operacion, se carga el cliente asociado a la operacion */
                            this.form.id_cliente = data.operacion.clientes_id;
                            this.form.cliente = data.operacion.cliente.nombre;
                            this.form.direccion_cliente =
                                data.operacion.cliente.direccion;
                            this.form.tasa_iva = data.operacion.tasa_iva;
                        }
                        this.form.parentesco_contratante =
                            data.parentesco_contratante;

                        /**limpio el formulario de ciertos datos solo cuando es una exhuamcion, no para modificar exhumacion */
                        if (this.getTipoformulario == "exhumacion") {
                            /**aqui voy */
                            this.form.inhumacion_b = 0;
                            this.form.velacion_b = 0;
                            this.form.aseguradora_b = 0;
                            this.form.misa_b = 0;
                            this.form.custodia_b = 0;

                            this.form.fechahora_inhumacion = "";
                            this.form.cremacion_b = 0;
                            this.form.fecha_cremacion = "";
                            this.form.fechahora_cremacion = "";
                            this.form.descripcion_urna = "";
                            this.form.traslado_b = 0;
                            this.form.fechahora_traslado = "";
                            this.form.destino_traslado = "";
                            this.form.fechahora_contrato = "";
                            this.form.parentesco_contratante = "";
                            this.form.id_cliente = "";
                            this.form.nota = "";
                            this.form.plan_funerario_futuro_b = this.sino[1];
                            this.form.plan_funerario_inmediato_b = this.sino[1];
                        } else {
                            this.form.nota = data.nota_servicio;
                            /**cargando los datos para el plan funerario a futuro en caso de que tenga uno en el contrato */
                            if (
                                data.plan_funerario_futuro_b == 1 &&
                                data.ventas_planes_id > 0
                            ) {
                                this.form.plan_funerario_futuro_b =
                                    this.sino[0];
                                /**el contrato tiene venta de plan funerario y se debe de cargar los conceptos */
                                this.form.id_convenio_plan =
                                    data.ventas_planes_id;
                                this.form.plan = data.plan_funerario_futuro;
                                this.secciones =
                                    data.plan_funerario_secciones_originales;
                                this.secciones_original =
                                    data.plan_funerario_secciones_originales;
                                this.datosPlanFunerario.nombre =
                                    data.nombre_titular_plan_funerario_futuro;
                                this.datosPlanFunerario.fecha_operacion_texto =
                                    data.plan_funerario_futuro_fecha_venta_texto;
                                this.datosPlanFunerario.operacion_status =
                                    data.plan_funerario_futuro_status;
                                this.datosPlanFunerario.status_texto =
                                    data.plan_funerario_futuro_status_texto;
                                this.datosPlanFunerario.saldo_neto =
                                    data.plan_funerario_futuro_saldo_restante;

                                if (data.tipos_contratante_id != "") {
                                    /**cargando el tipo de contratante*/
                                    this.tipos_contratante.forEach((tipo) => {
                                        if (
                                            tipo.value ==
                                            data.tipos_contratante_id
                                        ) {
                                            this.form.tipo_contratante = tipo;
                                            return;
                                        }
                                    });
                                }
                            } else {
                                this.form.plan_funerario_futuro_b =
                                    this.sino[1];
                                /**no tiene plan a futuro seleccionado y por lo tanto se debe verificar si cuenta con un plan de uso inmediato incluido */
                                if (
                                    data.plan_funerario_inmediato_b == 1 &&
                                    data.planes_funerarios_id > 0
                                ) {
                                    this.form.plan_funerario_inmediato_b =
                                        this.sino[0];
                                    /**aqui al cargarse los planes funerario se debe de dejar cargar y despues agregar el plan seleccionado como plan original */
                                } else {
                                    /**simplemente no hay plan funerario de uso inmediato */
                                    this.form.plan_funerario_inmediato_b =
                                        this.sino[1];
                                }
                            }
                            /**cargando articulos */
                            if (
                                data.operacion.movimientoinventario
                                    .articulosserviciofunerario.length > 0
                            ) {
                                data.operacion.movimientoinventario.articulosserviciofunerario.forEach(
                                    (articulo) => {
                                        this.form.articulos_servicios.push({
                                            id: articulo.articulos_id,
                                            codigo_barras:
                                                articulo.codigo_barras,
                                            tipo: articulo.tipo,
                                            categoria: articulo.categoria,
                                            descripcion: articulo.descripcion,
                                            // lote: articulo.lotes_id,
                                            //num_lote_inventario: articulo.num_lote_inventario,
                                            cantidad: articulo.cantidad,
                                            costo_neto_normal:
                                                articulo.costo_neto_normal,
                                            descuento_b: articulo.descuento_b,
                                            costo_neto_descuento:
                                                articulo.costo_neto_descuento,
                                            importe: articulo.importe,
                                            facturable_b: articulo.facturable_b,
                                            existencia: "N/A",
                                            plan_b: articulo.plan_b,
                                        });
                                    }
                                );
                            }
                            /**fin de cargar articulos */
                        }
                    } else {
                        /**no hay datos que mostrar y se cierra la ventana */
                    }
                    /**aqui cargo los datos obtenidos */
                    this.$vs.loading.close();
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

        async get_nacionalidades() {
            this.$vs.loading();
            await clientes
                .get_nacionalidades()
                .then((res) => {
                    //le agrego las nacionalidades
                    this.nacionalidades = [];
                    this.nacionalidades.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.nacionalidades.push({
                            label: element.nacionalidad,
                            value: element.id,
                        });
                    });
                    this.form.nacionalidad = this.nacionalidades[122];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_legistas_embalsamadores() {
            this.$vs.loading();
            await funeraria
                .get_legistas_embalsamadores()
                .then((res) => {
                    //le agrego las nacionalidades
                    this.legistas = [];
                    this.legistas.push({ label: "Seleccione 1", value: "" });
                    this.embsalsamadores = [];
                    this.embsalsamadores.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        if (element.tipo_persona == 1) {
                            //legista
                            this.legistas.push({
                                label: element.nombre,
                                value: element.id,
                            });
                        } else {
                            //embalsamador
                            this.embsalsamadores.push({
                                label: element.nombre,
                                value: element.id,
                            });
                        }
                    });
                    this.form.legista = this.legistas[0];
                    this.form.embsalsamador = this.embsalsamadores[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_titulos() {
            //this.$vs.loading();
            await funeraria
                .get_titulos()
                .then((res) => {
                    this.titulos = [];
                    this.titulos.push({ label: "Seleccione 1", value: "" });
                    res.data.forEach((element) => {
                        this.titulos.push({
                            label: element.titulo,
                            value: element.id,
                        });
                    });
                    this.form.titulo = this.titulos[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

        async get_estados_afectado() {
            //this.$vs.loading();
            await funeraria
                .get_estados_afectado()
                .then((res) => {
                    /**3,5,4 solo para exhumacion */
                    this.estados_cuerpo = [];
                    this.estados_cuerpo.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        if (this.esExhumacion) {
                            if (
                                element.id == 3 ||
                                element.id == 4 ||
                                element.id == 5
                            ) {
                                this.estados_cuerpo.push({
                                    label: element.estado,
                                    value: element.id,
                                });
                            }
                        } else {
                            this.estados_cuerpo.push({
                                label: element.estado,
                                value: element.id,
                            });
                        }
                    });
                    this.form.estado_cuerpo = this.estados_cuerpo[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_estados_civiles() {
            //this.$vs.loading();
            await funeraria
                .get_estados_civiles()
                .then((res) => {
                    this.estados_civiles = [];
                    this.estados_civiles.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.estados_civiles.push({
                            label: element.estado,
                            value: element.id,
                        });
                    });
                    this.form.estado_civil = this.estados_civiles[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_escolaridades() {
            //this.$vs.loading();
            await funeraria
                .get_escolaridades()
                .then((res) => {
                    this.escolaridades = [];
                    this.escolaridades.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.escolaridades.push({
                            label: element.escolaridad,
                            value: element.id,
                        });
                    });
                    this.form.escolaridad = this.escolaridades[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_lugares_velacion() {
            //this.$vs.loading();
            await funeraria
                .get_lugares_velacion()
                .then((res) => {
                    this.lugares_servicio = [];
                    this.lugares_servicio.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.lugares_servicio.push({
                            label: element.lugar,
                            value: element.id,
                        });
                    });
                    this.form.lugar_servicio = this.lugares_servicio[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_lugares_inhumacion() {
            //this.$vs.loading();
            await funeraria
                .get_lugares_inhumacion()
                .then((res) => {
                    this.cementerios_servicio = [];
                    this.cementerios_servicio.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.cementerios_servicio.push({
                            label: element.cementerio,
                            value: element.id,
                        });
                    });
                    this.form.cementerio_servicio =
                        this.cementerios_servicio[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_afiliaciones() {
            //this.$vs.loading();
            await funeraria
                .get_afiliaciones()
                .then((res) => {
                    this.afiliaciones = [];
                    this.afiliaciones.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.afiliaciones.push({
                            label: element.afiliacion,
                            value: element.id,
                        });
                    });
                    this.form.afiliacion = this.afiliaciones[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },
        async get_sitios_muerte() {
            //this.$vs.loading();
            await funeraria
                .get_sitios_muerte()
                .then((res) => {
                    this.sitios_muerte = [];
                    this.sitios_muerte.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.sitios_muerte.push({
                            label: element.sitio,
                            value: element.id,
                        });
                    });
                    this.form.sitio_muerte = this.sitios_muerte[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

        async get_tipos_contratante() {
            //this.$vs.loading();
            await funeraria
                .get_tipos_contratante()
                .then((res) => {
                    this.tipos_contratante = [];
                    this.tipos_contratante.push({
                        label: "Seleccione 1",
                        value: "",
                    });
                    res.data.forEach((element) => {
                        this.tipos_contratante.push({
                            label: element.tipo,
                            value: element.id,
                        });
                    });
                    this.form.tipo_contratante = this.tipos_contratante[0];
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

        async get_material_velacion() {
            //this.$vs.loading();
            await funeraria
                .get_material_velacion()
                .then((res) => {
                    this.material_velacion = [];
                    res.data.forEach((element) => {
                        this.form.material_velacion.push({
                            id: element.id,
                            descripcion: element.descripcion,
                            nota: "",
                            cantidad: 0,
                        });
                    });
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

        async get_planes_uso_inmediato() {
            this.$vs.loading();
            await funeraria
                .get_planes()
                .then((res) => {
                    this.planes_funerarios = [];
                    this.planes_funerarios.push({
                        label: "Seleccione 1",
                        value: "",
                        plan: "",
                        secciones: [],
                        costo_neto: 0,
                    });
                    res.data.forEach((plan) => {
                        plan.precios.forEach((precio) => {
                            if (precio.financiamiento == 1) {
                                this.planes_funerarios.push({
                                    label: plan.plan,
                                    value: plan.id,
                                    plan: plan.plan,
                                    secciones: plan.secciones,
                                    costo_neto: precio.costo_neto,
                                });
                            }
                        });
                    });
                    /**aqui se verifica si existen datos de algun plan fuenrario que haya sido cargado desde la bd por el contrato */
                    if (this.data_contrato != []) {
                        if (
                            this.data_contrato.plan_funerario_inmediato_b == 1
                        ) {
                            /**el contrato tiene un plan funerario de uso inmediato */
                            this.planes_funerarios.push({
                                label:
                                    this.data_contrato.plan_funerario_original +
                                    " (PLAN ORIGINAL)",
                                value: this.data_contrato.planes_funerarios_id,
                                plan: this.data_contrato
                                    .plan_funerario_original,
                                secciones:
                                    this.data_contrato
                                        .plan_funerario_secciones_originales,
                                costo_neto:
                                    this.data_contrato.costo_plan_original,
                            });
                        }
                        //se selecciona este por defecto
                        if (this.form.plan_funerario_inmediato_b.value == 1) {
                            this.form.plan_funerario =
                                this.planes_funerarios[
                                    this.planes_funerarios.length - 1
                                ];
                        }
                    } else {
                        this.form.plan_funerario = this.planes_funerarios[0];
                    }

                    this.$vs.loading.close();
                })
                .catch((err) => {
                    this.$vs.loading.close();
                });
        },

        get_concepto_por_codigo(origen = "", evento = "") {
            if (evento == "blur") {
                return;
            } else {
                /**checando el origen */
                if (origen == "codigo_barras") {
                    if (this.serverOptions.numero_control.trim() == "") {
                        //return;
                    }
                }
            }

            let self = this;
            if (funeraria.cancel) {
                funeraria.cancel("Operation canceled by the user.");
            }
            this.$vs.loading();
            funeraria
                .get_inventario_servicios_codigos(this.serverOptions)
                .then((res) => {
                    if (res.data.length > 0) {
                        let datos = res.data[0];
                        /**agrego el concepto al listado del contrato */
                        this.form.articulos_servicios.push({
                            id: datos.id,
                            codigo_barras: datos.codigo_barras,
                            tipo: datos.tipo_articulo.tipo,
                            categoria: datos.categoria.categoria,
                            descripcion: datos.descripcion,
                            //lote: lote.lotes_id,
                            //num_lote_inventario: lote.num_lote_inventario,
                            cantidad: 1,
                            costo_neto_normal: datos.precio_venta,
                            descuento_b: 0,
                            costo_neto_descuento: 0,
                            importe: 0,
                            facturable_b: datos.grava_iva_b,
                            existencia: datos.existencia,
                            plan_b: 0,
                        });
                    } else {
                        this.$vs.notify({
                            title: "Busar artículos y servicios",
                            text: "No se ha encontrado el concepto con el número de clave ingresado.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 8000,
                        });
                    }
                    this.$vs.loading.close();
                    this.serverOptions.numero_control = "";
                    this.$nextTick(() =>
                        this.$refs["codigo_barras"].$el
                            .querySelector("input")
                            .focus()
                    );
                })
                .catch((err) => {
                    this.$vs.loading.close();
                    this.ver = true;
                    if (err.response) {
                        if (err.response.status == 403) {
                            /**FORBIDDEN ERROR */
                            this.$vs.notify({
                                title: "Permiso denegado",
                                text: "Verifique sus permisos con el administrador del sistema.",
                                iconPack: "feather",
                                icon: "icon-alert-circle",
                                color: "warning",
                                time: 8000,
                            });
                        }
                    }
                });
        },

        acceptAlert() {
            this.$validator
                .validateAll()
                .then((result) => {
                    if (!result) {
                        this.$vs.notify({
                            title: "Error",
                            text: "Verifique que todos los datos han sido capturados",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            position: "bottom-right",
                            time: "12000",
                        });
                        return;
                    } else {
                        //AL LLEGAR AQUI SE SABE QUE EL FORMULARIO PASO LA VALIDACION
                        (async () => {
                            /**es modificacion */
                            this.callback = await this.modificar_contrato;
                            this.openPassword = true;
                        })();
                    }
                })
                .catch(() => {});
        },

        async modificar_contrato() {
            //aqui mando guardar los datos
            this.errores = [];
            this.$vs.loading();
            try {
                this.form.id_servicio = this.get_id_solicitud;
                let res = await funeraria.modificar_contrato(
                    this.form,
                    this.getTipoformulario
                );
                if (res.data >= 1) {
                    //success
                    this.$vs.notify({
                        title: "Contrato Funerario",
                        text: "Se ha modificado el contrato correctamente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "success",
                        time: 5000,
                    });
                    this.$emit("ver_pdfs_nueva_venta", res.data);
                    this.cerrarVentana();
                } else {
                    this.$vs.notify({
                        title: "Contrato Funerario",
                        text: "Error al modificar el contrato, por favor reintente.",
                        iconPack: "feather",
                        icon: "icon-alert-circle",
                        color: "danger",
                        time: 6000,
                    });
                }

                this.$vs.loading.close();
            } catch (err) {
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text: "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000,
                        });
                    } else if (err.response.status == 422) {
                        //checo si existe cada error
                        this.errores = err.response.data.error;

                        this.$vs.notify({
                            title: "Contrato Funerario",
                            text: "Verifique los errores encontrados en los datos.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 5000,
                        });
                    } else if (err.response.status == 409) {
                        //este error es por alguna condicion que el contrano no cumple para modificar
                        //la propiedad esa ya ha sido vendida
                        this.$vs.notify({
                            title: "Modificar información del contrato",
                            text: err.response.data.error,
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "danger",
                            time: 3000,
                        });
                    }
                }
                this.$vs.loading.close();
            }
        },
        cancel() {
            this.$emit("closeVentana");
        },

        cancelar() {
            this.botonConfirmarSinPassword = "Salir y limpiar";
            this.accionConfirmarSinPassword =
                "Esta acción limpiará los datos que capturó en el formulario.";
            this.openConfirmarSinPassword = true;
            this.callBackConfirmar = this.cerrarVentana;
        },

        //remover beneficiario
        remover_articulo(index_articulo_servicio) {
            this.botonConfirmarSinPassword = "eliminar";
            this.accionConfirmarSinPassword =
                "¿Desea eliminar este concepto? Los datos quedarán eliminados del sistema?";
            this.form.index_articulo_servicio = index_articulo_servicio;
            this.callBackConfirmar = this.remover_articulo_callback;
            this.openConfirmarSinPassword = true;
        },
        //remover beneficiario callback quita del array al beneficiario seleccionado
        remover_articulo_callback() {
            this.form.articulos_servicios.splice(
                this.form.index_articulo_servicio,
                1
            );
        },

        cerrarVentana() {
            this.openConfirmarSinPassword = false;
            this.limpiarVentana();
            this.$emit("closeVentana");
        },
        //regresa los datos a su estado inicial
        limpiarVentana() {
            this.saldo_neto_terreno = 0;
            this.data_contrato = [];
            this.activeTab = 0;
            this.form.nombre_afectado = "";
            this.form.fecha_nacimiento = "";
            this.form.edad = "";
            this.form.genero = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.titulo = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.nacionalidad = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.direccion_fallecido = "";
            this.form.lugar_nacimiento = "";
            this.form.ocupacion = "";
            this.form.estado_civil = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.afiliacion = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.escolaridad = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.afiliacion_nota = "";

            /*DATOS DEL CERTIFICADO MEDICO*/
            this.form.folio_certificado = "";
            this.form.fechahora_defuncion = "";
            this.form.causa_muerte = "";
            this.form.muerte_natural_b = {
                value: "1",
                label: "SI",
            };
            this.form.sitios_muerte = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.lugar_muerte = "";
            this.form.atencion_medica_b = {
                value: "1",
                label: "SI",
            };
            this.form.contagioso_b = {
                value: "1",
                label: "SI",
            };
            this.form.enfermedades_padecidas = "";
            this.form.certificado_informante = "";
            this.form.certificado_informante_telefono = "";
            this.form.certificado_informante_parentesco = "";
            this.form.medico_legista = "";
            this.form.cedula_legista = "";
            this.form.estados_cuerpo = {
                value: "",
                label: "Seleccione 1",
            };
            /**DESTINOS DEL SERVICIO */
            this.form.embalsamar_b = 0;
            this.form.medico_responsable_embalsamado = "";
            this.form.preparador = "";
            this.form.cedula_embalsamador = "";
            this.form.velacion_b = 0;
            this.form.lugar_servicio = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.direccion_velacion = "";

            this.form.cremacion_b = 0;
            this.form.fechahora_cremacion = "";
            this.form.fechahora_entrega_cenizas = "";
            this.form.descripcion_urna = "";
            this.form.inhumacion_b = 0;
            this.form.cementerio_servicio = {
                value: "",
                label: "Seleccione 1",
            };
            this.form.fechahora_inhumacion = "";
            this.form.ubicacion = "";
            this.form.ubicacion_convenio = "";
            this.form.ventas_terrenos_id = 0;

            this.form.traslado_b = 0;
            this.form.fechahora_traslado = "";
            this.form.destino_traslado = "";

            this.form.aseguradora_b = 0;
            this.form.numero_convenio_aseguradora = "";
            this.form.aseguradora = "";
            this.form.telefono_aseguradora = "";

            this.form.misa_b = 0;
            this.form.iglesia_misa = "";
            this.form.direccion_iglesia = "";
            this.form.fechahora_misa = "";

            this.form.custodia_b = 0;
            this.form.responsable_custodia = "";
            this.form.folio_custodia = "";
            this.form.folio_liberacion = "";

            this.form.material_velacion_b = 0;
            this.form.material_velacion = [];

            this.form.acta_b = 0;
            this.form.fecha_acta = "";
            this.form.folio_acta = "";

            this.form.fechahora_contrato = "";

            this.form.id_cliente = "";
            this.form.cliente = "";
            this.form.direccion_cliente = "";
            this.form.parentesco_contratante = "";

            this.form.plan_funerario_futuro_b = { value: "0", label: "NO" };
            this.form.id_convenio_plan = "";
            this.form.tipo_contratante = {
                value: "",
                label: "Seleccione 1",
            };

            this.form.plan = "";
            this.secciones = [];
            this.datosPlanFunerario = [];
            this.secciones_original = [];

            this.form.plan_funerario_inmediato_b = { value: "0", label: "NO" };
            this.form.plan_funerario = {
                value: "",
                label: "Seleccione 1",
                secciones: [],
                costo_neto: 0,
            };

            this.form.articulos_servicios = [];
            this.form.tasa_iva = 16;
            this.form.nota = "";
        },

        closePassword() {
            this.openPassword = false;
        },

        clienteSeleccionado(datos) {
            /**obtiene los datos retornados del buscar cliente */
            this.form.cliente = datos.nombre;
            this.form.id_cliente = datos.id_cliente;
            this.form.direccion_cliente = datos.datos.direccion;
            //alert(datos.id_cliente);
        },

        TerrenoSeleccionado(datos) {
            /**obtiene los datos retornados del buscar cliente */
            this.saldo_neto_terreno = datos.saldo_neto;
            this.form.ubicacion_convenio = datos.ubicacion;
            this.form.ventas_terrenos_id = datos.numero_control;
            //alert(datos.id_cliente);
        },

        PlanSeleccionado(datos) {
            this.datosPlanFunerario = datos;
            /**obtiene los datos retornados del buscar cliente */
            this.form.plan =
                datos.venta_plan.nombre_original +
                " (Convenio " +
                datos.numero_convenio +
                ")";
            this.form.id_convenio_plan = datos.ventas_planes_id;

            this.secciones = datos.venta_plan.secciones_original;
            this.secciones_original = datos.venta_plan.secciones_original;
        },

        LoteSeleccionado(datos) {
            this.form.articulos_servicios.push(datos);
            /**agregando los datos a la lista de articulos y servicios */
        },

        limpiarTerreno() {
            this.form.ventas_terrenos_id = "";
            this.form.ubicacion_convenio = "";
        },
        quitarTerreno() {
            this.botonConfirmarSinPassword = "Cambiar de Ubicación";
            this.accionConfirmarSinPassword =
                "¿Desea cambiar de ubicación para este contrato?";
            this.callBackConfirmar = this.limpiarTerreno;
            this.openConfirmarSinPassword = true;
        },

        limpiarPlan() {
            this.form.id_convenio_plan = "";
            this.form.plan = "";
            this.secciones = [];
            this.secciones_original = [];
        },
        quitarPlan() {
            this.botonConfirmarSinPassword = "Cambiar Convenio";
            this.accionConfirmarSinPassword =
                "¿Desea cambiar de plan funerario para este contrato?";
            this.callBackConfirmar = this.limpiarPlan;
            this.openConfirmarSinPassword = true;
        },

        limpiarCliente() {
            this.form.id_cliente = "";
            this.form.cliente = "";
            this.form.direccion_cliente = "";
        },
        quitarCliente() {
            this.botonConfirmarSinPassword = "Cambiar cliente";
            this.accionConfirmarSinPassword =
                "¿Desea cambiar de cliente para este contrato?";
            this.callBackConfirmar = this.limpiarCliente;
            this.openConfirmarSinPassword = true;
        },
        async consultar_venta_id() {
            try {
                this.$vs.loading();
                //let res = await planes.consultar_venta_id(this.form.id_venta);
                //this.datosVenta = res.data[0];

                /**cargando la antiguedad de la venta */
                /* this.ventasAntiguedad.forEach((element) => {
                  if (element.value == this.datosVenta.antiguedad_operacion_id) {
                    this.form.ventaAntiguedad = element;
                    return;
                  }
                });
                */
                this.$vs.loading.close();
            } catch (err) {
                this.$vs.loading.close();
                if (err.response) {
                    if (err.response.status == 403) {
                        /**FORBIDDEN ERROR */
                        this.$vs.notify({
                            title: "Permiso denegado",
                            text: "Verifique sus permisos con el administrador del sistema.",
                            iconPack: "feather",
                            icon: "icon-alert-circle",
                            color: "warning",
                            time: 4000,
                        });
                    }
                }
            }
        },
        limpiarValidation() {
            this.$validator.pause();
            this.$nextTick(() => {
                this.$validator.errors.clear();
                this.$validator.fields.items.forEach((field) => field.reset());
                this.$validator.fields.items.forEach((field) =>
                    this.errors.remove(field)
                );
                this.$validator.resume();
            });
        },
    },
    // Lifecycle hooks
    created() {
        this.$log("Component created! " + this.$options.name); // reactive data is ready, DOM not yet
    },
    mounted() {
        this.$log("Component mounted! " + this.$options.name);
    },
    beforeDestroy() {
        this.$popupManager.unregister(this.$options.name);
    },
    destroyed() {
        this.$log("Component destroyed! " + this.$options.name); // reactive data is ready, DOM not yet
    },
};
</script>
