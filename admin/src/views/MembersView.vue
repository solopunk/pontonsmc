<script setup>
import Layout from "@/components/layouts/Layout.vue";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import {
  BarsArrowUpIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
} from "@heroicons/vue/20/solid";

const loading = ref(true);
const members = ref([]);
const page = ref(1);
const hasMorePages = ref(false);

const fetchMembers = async () => {
  try {
    const response = await axios.get("/api/member", {
      params: { page: page.value },
    });
    members.value = response.data.data;
    hasMorePages.value = response.data.current_page < response.data.last_page;
  } catch (error) {
    console.error("Error fetching members:", error);
  } finally {
    loading.value = false;
  }
};

// // Functions to navigate between pages
// const nextPage = () => {
//   if (hasMorePages.value) {
//     page.value += 1;
//   }
// };

// const previousPage = () => {
//   if (page.value > 1) {
//     page.value -= 1;
//   }
// };

watch(page, fetchMembers);
onMounted(fetchMembers);
</script>

<template>
  <Layout :loading>
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">Users</h1>
        <p class="mt-2 text-sm text-gray-700">
          A list of all the users in your account including their name, title,
          email and role.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
          type="button"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          Add user
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
                  scope="col"
                  class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                >
                  Nom
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Courriel
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Type
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  À jour de cotisation
                </th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                  <span class="sr-only">Edit</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="member in members" :key="member.email">
                <td
                  class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                >
                  {{ member.first + " " + member.last }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ member.email }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ member.type }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ member.upToDate }}
                </td>
                <td
                  class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                >
                  <RouterLink
                    class="text-indigo-600 hover:text-indigo-900"
                    :to="{ name: 'member', params: { id: member.id } }"
                  >
                    Edit<span class="sr-only">, {{ member.name }}</span>
                  </RouterLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>
