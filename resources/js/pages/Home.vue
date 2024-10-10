<template>
    <div class="sticky top-0 bg-neutral-100 dark:bg-neutral-900" id="content-header">
        <h1 class="text-xl md:text-2xl text-left text-neutral-900 dark:text-white" v-if="!show_header">Chào mừng bạn đến với Drive</h1>
        <h1 class="text-xl md:text-2xl text-center text-neutral-900 dark:text-white" v-if="show_header">Chào mừng bạn đến với Drive</h1>
        <div class="flex justify-center items-center mt-3" v-if="scroll_y == 0">
            <div class="w-full md:w-[50%]">
                <IconField class="font-semibold grow">
                    <InputIcon class="pi pi-search" />
                    <InputText
                        v-model="search"
                        placeholder="Tìm trong Drive"
                        class="rounded-full w-full border-none dark:bg-neutral-700 bg-neutral-200"
                    />
                </IconField>
            </div>
        </div>
    </div>
    <div v-for="item in 100" class="mt-3">
        {{ item }}
    </div>
</template>
<script>
export default {
    name: "Home",
    data() {
        return {
            search: "",
            scroll_y: 0,
            show_header: true,
        };
    },
    watch: {
        '$parent.$parent.$parent.$data.scroll_y': function(scroll_y) {
            this.scroll_y = scroll_y;
            let element = document.getElementById("content-header");
            let height = 0;
            if (element) {
                height = element.offsetHeight;
            }
            if (scroll_y > height) {
                this.show_header = false;
            } else {
                this.show_header = true;
            }
        }
    },
};
</script>
<style lang=""></style>
