<template>
    <div class="container container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card-body" v-if="$gate.allow('viewAll', 'apiUser')">
                    <passport-clients></passport-clients>
                    <passport-authorized-clients></passport-authorized-clients>
                    <passport-personal-access-tokens></passport-personal-access-tokens>
                </div>
                <div class="card" v-else>
                    <div class="card-header">
                        <h3 class="card-title text-center text-red">You have no permissions</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

    }
</script>
