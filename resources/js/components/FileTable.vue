<template>
    <Table
        class="text-black dark:text-white"
        @scroll="$emit('onScrollEvent', $event)"
    >
        <TableHeader>
            <TableRow>
                <TableHead v-for="column in columns" :key="column.name">
                    {{ column.label }}
                </TableHead>
                <TableHead> </TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <template v-if="data.length === 0 && !loading">
                <TableRow>
                    <TableCell colspan="100%">
                        <div class="flex items-center justify-center h-full">
                            <span class="text-sm text-muted-foreground">{{
                                $t("table_empty")
                            }}</span>
                        </div>
                    </TableCell>
                </TableRow>
            </template>
            <template v-else>
                <TableRow v-for="row in data" :key="row.id">
                    <TableCell
                        v-for="column in columns"
                        :key="column.name"
                        :class="column.class + ' py-2'"
                        @dblclick="$emit('rowClick', row)"
                    >
                        <div class="flex items-center">
                            <template v-if="column.has_icon">
                                <img :src="row.icon" alt="" class="w-8 h-8" />
                            </template>
                            <span :class="column.has_icon ? 'ml-3' : ''">
                                {{ row[column.name] }}
                            </span>
                        </div>
                    </TableCell>
                    <TableCell class="p-1">
                        <div
                            class="flex items-center justify-end text-right"
                        >
                            <button
                                class="flex items-center justify-center p-3 text-lg rounded-full w-9 h-9 hover:bg-neutral-400 hover:bg-opacity-50"
                                @click="copyUrl(row)"
                            >
                                <i class="fa-light fa-clipboard"></i>
                            </button>
                            <button
                                class="flex items-center justify-center p-3 text-lg rounded-full w-9 h-9 hover:bg-neutral-400 hover:bg-opacity-50"
                                @click="downloadFile(row.download_url)"
                                v-if="row.type !== 'folder'"
                            >
                                <i
                                    class="fa-light fa-arrow-down-to-bracket"
                                ></i>
                            </button>
                        </div>
                    </TableCell>
                </TableRow>
            </template>
            <template v-if="loading">
                <TableRow>
                    <TableCell colspan="100%">
                        <div class="flex items-center justify-center h-full">
                            <!-- <span class="text-sm text-muted-foreground animate-spin">
                <i class="text-2xl fa-duotone fa-solid fa-loader"></i>
              </span> -->
                            <div class="loading-wrapper">
                                <div class="blue ball"></div>
                                <div class="red ball"></div>
                                <div class="yellow ball"></div>
                                <div class="green ball"></div>
                            </div>
                        </div>
                    </TableCell>
                </TableRow>
            </template>
        </TableBody>
    </Table>
</template>
<script>
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { useToast } from "@/components/ui/toast/use-toast";
const { toast } = useToast();
export default {
    components: {
        Table,
        TableBody,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
    },
    props: {
        data: Array,
        columns: Array,
        loading: Boolean,
        show_load_more: Boolean,
    },
    methods: {
        loadMore() {
            this.$emit("loadMore");
        },
        downloadFile(url) {
            window.open(url);
        },
        copyUrl(row) {
            if (row.type === "folder") {
                let url = this.$router.resolve({
                    name: "folders.show",
                    params: { id: row.id },
                }).href;
                url = window.location.origin + url;
                navigator.clipboard.writeText(url);
            } else {
                navigator.clipboard.writeText(row.download_url);
            }
            toast({
                title: this.$t("copied_to_clipboard"),
            });
        },
    },
};
</script>
