<template>
    <Table class="text-black dark:text-white">
        <TableHeader>
            <TableRow>
                <TableHead
                    v-for="column in columns"
                    :key="column.name"
                    class="relative"
                >
                    {{ column.label }}
                    <span
                        class="absolute top-[30%] right-4"
                        v-if="column.sortable"
                        @click="$emit('sort')"
                    >
                        <i
                            class="fa-solid fa-arrow-up"
                            v-if="column.sortable === 'asc'"
                        ></i>
                        <i
                            class="fa-solid fa-arrow-down"
                            v-if="column.sortable === 'desc'"
                        ></i>
                    </span>
                </TableHead>
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
                        :class="column.class"
                    >
                        <div class="flex items-center">
                            <template v-if="column.has_icon">
                                <FileIcons
                                    name="test.txt"
                                    width="30"
                                    height="30"
                                    :isFolder="row.type === 'folder'"
                                    :isMulti="false"
                                    :style="{ float: 'left' }"
                                />
                            </template>
                            <span class="ml-2">
                                {{ row[column.name] }}
                            </span>
                        </div>
                    </TableCell>
                </TableRow>
            </template>
            <template v-if="loading">
                <TableRow>
                    <TableCell colspan="100%">
                        <div class="flex items-center justify-center h-full">
                            <span
                                class="text-sm text-muted-foreground animate-spin"
                            >
                                <i
                                    class="text-2xl fa-duotone fa-solid fa-loader"
                                ></i>
                            </span>
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
import FileIcons from "file-icons-vue";
export default {
    components: {
        Table,
        TableBody,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
        FileIcons,
    },
    props: {
        data: Array,
        columns: Array,
        loading: Boolean,
    },
};
</script>
<style lang=""></style>
