<template>
    <div
        class="sticky top-0 text-black bg-neutral-100 dark:bg-neutral-900 dark:text-white"
        id="content-header"
    >
        <h1
            class="text-xl text-left md:text-2xl text-neutral-900 dark:text-white"
            v-if="!show_header"
        >
            Chào mừng bạn đến với Drive
        </h1>
        <h1
            class="mb-5 text-xl text-center md:text-2xl text-neutral-900 dark:text-white"
            v-if="show_header"
        >
            Chào mừng bạn đến với Drive
        </h1>
        <div class="flex justify-center" v-if="scroll_y == 0">
            <div class="relative w-full md:w-[50%] lg:w-[35%]">
                <Input
                    id="search"
                    type="text"
                    :placeholder="$t('search')"
                    class="pl-10 rounded-full"
                    v-model="this.$parent.$parent.$parent.search"
                />
                <span
                    class="absolute inset-y-0 flex items-center justify-center px-2 start-2"
                >
                    <i class="fa-regular fa-magnifying-glass"></i>
                </span>
                <span
                    class="absolute inset-y-0 flex items-center justify-center px-2 end-2"
                    @click="this.$parent.$parent.$parent.search = ''"
                    v-if="this.$parent.$parent.$parent.search"
                >
                    <i class="fa-solid fa-circle-xmark"></i>
                </span>
            </div>
        </div>
    </div>
</template>
<script>
import { Input } from "@/components/ui/input";

export default {
    name: "Home",
    components: {
        Input,
    },
    data() {
        return {
            scroll_y: 0,
            show_header: true,
            data: [
                [1, 2],
                [3, 4],
            ],
            columns: [
                {
                    name: "name",
                    label: "Tên",
                    sortable: true,
                },
                {
                    name: "size",
                    label: "Kích thước",
                    sortable: true,
                },
                {
                    name: "modified",
                    label: "Ngày cập nhật",
                    sortable: true,
                },
            ],
        };
    },
    mounted() {
        console.log($.ajax("/api/drive/root"));

    },
    watch: {
        "$parent.$parent.$parent.$data.scroll_y": function (scroll_y) {
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
        },
    },
};
</script>
<style lang=""></style>
