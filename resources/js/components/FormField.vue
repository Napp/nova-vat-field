<template>
    <default-field :field="field">
        <template slot="field">
            <input :id="field.name" type="text"
                   :class="inputClass"
                   :placeholder="field.name"
                   v-model="value"
                   @blur="checkVat"
            />

            <p v-if="hasError" class="my-2 text-danger">
                {{ firstError }}
            </p>
        </template>
    </default-field>
</template>

<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'
    let jsvat = require('jsvat');

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        data: function () {
            return {
                hasError: false,
                value: ''
            }
        },

        computed: {
            inputClass: function () {
                let classList = 'w-full form-control form-input form-input-bordered';

                if(! this.value) {
                    return classList;
                }

                if (this.hasError) {
                    return`${classList} border-danger`;
                }

                return `${classList} border-success`;
            }
        },

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || ''
            },

            checkVat() {
                let result = jsvat.checkVAT(this.value);
                if (!result.isValid) {
                    this.hasError = true;

                    return;
                }

                this.hasError = false;
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value || '')
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },
        }
    }
</script>