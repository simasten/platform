<template>
    <div :class="`text-${theme}-lighten-5`" class="mx-auto" style="width: 76px">
        <div class="circle">
            <div class="position-relative h-100 w-100 text-white">
                <v-avatar :color="`${theme}`" elevation="6" size="64">
                    <v-icon color="white">{{ icon ?? page.icon }}</v-icon>
                </v-avatar>
            </div>
        </div>
    </div>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-icon",

    props: {
        icon: String,
    },

    setup() {
        const store = usePageStore();

        const { page, theme } = storeToRefs(store);

        return {
            page,
            theme,
        };
    },
};
</script>
