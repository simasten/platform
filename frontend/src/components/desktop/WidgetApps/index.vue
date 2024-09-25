<template>
    <v-hover>
        <template v-slot:default="{ isHovering, props }">
            <v-sheet
                v-bind="props"
                :max-width="maxWidth"
                class="text-center mx-auto cursor-pointer"
                rounded="md"
                @click="$emit('click')"
                flat
            >
                <v-card-text class="pa-1 text-center">
                    <v-avatar
                        :color="`${color}-lighten-1`"
                        :class="isHovering ? 'elevation-6' : 'elevation-0'"
                        class="position-relative"
                        style="font-size: 16px"
                        size="64"
                        rounded="pill"
                    >
                        <v-icon
                            v-if="isHovering"
                            class="position-absolute"
                            color="rgba(255, 255, 255, 0.06)"
                            :icon="icon"
                            :size="64"
                            style="z-index: 0"
                        ></v-icon>

                        <v-icon
                            class="position-relative v-icon--wght-250"
                            :color="highlight"
                            :icon="icon"
                            :size="32"
                            style="z-index: 1"
                        ></v-icon>
                    </v-avatar>
                </v-card-text>

                <v-card-text class="pt-2">
                    <div
                        class="text-caption font-weight-default d-flex justify-center w-100"
                        style="line-height: 1.1em"
                    >
                        {{ label }}
                    </div>
                </v-card-text>
            </v-sheet>
        </template>
    </v-hover>
</template>

<script>
export default {
    name: "widget-apps",

    props: {
        color: String,
        highlight: String,
        icon: String,
        label: String,
        maxWidth: {
            type: String,
            default: "96",
        },
    },

    emits: {
        click: null,
    },
};
</script>
