<template>
    <form>
        <div class="form-group">
            <div class="form-control">
                <label for="assign_support_agent">Assign To:</label>
                <select v-on:change="assignAgent" v-model="fields.agentId" name="assign_support_agent" id="assign_support_agent">
                    <option v-for="agent in supportAgents"
                            :value="agent.id">{{ agent.name }}</option>
                </select>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        props: [
            'ticketId',
            'assignedSupportAgentId'
        ],
        data() {
            return {
                supportAgents: null,
                fields: {
                    agentId: this.assignedSupportAgentId
                }
            }
        },
        mounted() {
            axios.get('/tickets/supportagents').then(response => {
                this.supportAgents = response.data;
            })
        },
        methods: {
            assignAgent: function () {
                axios.post('/tickets/'+this.ticketId+'/supportagents/'+this.fields.agentId).then(response => {
                }).catch(error => {
                    console.log('Error: '+error);
                })
            }
        }
    }
</script>
