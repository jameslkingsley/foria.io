<template>
    <div>
        <button :class="buttonClasses" :disabled="report" @click.prevent="open">
            <i class="material-icons m-r-1">report_problem</i>
            Report
        </button>

        <f-modal-form title="Report" confirm="Complete" @submit="submit" :active.sync="isReporting" v-if="! report">
            <b-field label="Select a reason for this report">
                <b-select placeholder="Select a reason" name="reason" required>
                    <option
                        v-for="reason in reasonsList"
                        :value="reason.key"
                        :key="reason.key">
                        {{ reason.text }}
                    </option>
                </b-select>
            </b-field>

            <b-field label="Description (optional)">
                <b-input name="body" type="textarea"></b-input>
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
                report: null,
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
                return ajax.post(`/api/report/${this.reference}`, data)
                    .then(r => {
                        this.fetch().then(r => {
                            this.$toast.open({
                                message: 'Report Sent',
                                type: 'is-success',
                                duration: 4000
                            });
                        });
                    });
            },

            fetch() {
                return ajax.get(`/api/report/${this.reference}`)
                    .then(r => this.report = r.data.data);
            }
        },

        created() {
            this.fetch();
        }
    }
</script>
