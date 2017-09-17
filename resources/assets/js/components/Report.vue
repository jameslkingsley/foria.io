<template>
    <div>
        <button :class="buttonClasses" @click.prevent="open">
            Report
        </button>

        <f-modal-form title="Report" confirm="Complete" @submit="submit" :active.sync="isReporting">
            <b-field label="Select a reason for this report">
                <b-select placeholder="Select a reason" name="reason">
                    <option
                        v-for="reason in reasonsList"
                        :value="reason.key"
                        :key="reason.key">
                        {{ reason.text }}
                    </option>
                </b-select>
            </b-field>
        </f-modal-form>
    </div>
</template>

<script>
    export default {
        props: {
            reference: { type: String }
        },

        data() {
            return {
                reasons: Foria.reportableReasons,
                isReporting: false
            };
        },

        computed: {
            buttonClasses() {
                return {
                    'button': true
                };
            },

            reasonsList() {
                let list = [];

                for (let reason in this.reasons) {
                    list.push({
                        key: reason,
                        text: this.reasons[reason]
                    });
                }

                return list;
            }
        },

        methods: {
            open() {
                this.isReporting = true;
            },

            submit(data) {
                ajax.post(`/api/report/${this.reference}`, data)
                    .then(r => {
                        console.log(r.data);
                    });
            }
        }
    }
</script>
