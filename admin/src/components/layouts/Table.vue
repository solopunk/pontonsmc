<script setup>
const props = defineProps({
  cells: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    required: true,
  },
  edit: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
      <h2 class="text-base font-semibold leading-6 text-gray-900">
        <slot name="sub" />
      </h2>
      <p class="mt-2 text-sm text-gray-700">
        <slot name="txt" />
      </p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
      <button
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
      >
        <slot name="btn" />
      </button>
    </div>
  </div>
  <div class="mt-8 flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <table class="min-w-full divide-y divide-gray-300">
          <thead>
            <tr>
              <th
                v-for="(cell, index) in props.cells"
                :key="index"
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                :class="index === 0 && 'sm:pl-0'"
              >
                {{ cell.title }}
              </th>
              <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                <span class="sr-only">Edit</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="item in props.data" :key="item.id">
              <td
                v-for="(cell, index) in props.cells"
                :key="index"
                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                :class="index === 0 && 'sm:pl-0'"
              >
                {{ item[cell.key] }}
              </td>
              <td
                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
              >
                <RouterLink
                  class="text-indigo-600 hover:text-indigo-900"
                  :to="{ name: props.edit, params: { id: item.id } }"
                >
                  Edit<span class="sr-only">, {{ item.title }}</span>
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
