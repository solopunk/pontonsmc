<script setup>
const model = defineModel();

const radios = [
  { value: false, title: "Non" },
  { value: true, title: "Oui" },
];
</script>

<template>
  <fieldset class="col-span-full">
    <legend class="text-sm font-semibold leading-6 text-gray-900">
      Copropriétaire
    </legend>
    <p class="mt-1 text-sm leading-6 text-gray-600">
      Le bateau est-il détenu en copropriété ?
    </p>
    <div
      class="mt-6 space-y-6 sm:flex sm:items-center sm:space-x-10 sm:space-y-0"
    >
      <div v-for="radio in radios" :key="radio.value" class="flex items-center">
        <input
          :id="radio.value"
          name="notification-method"
          type="radio"
          class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"
          :value="radio.value"
          v-model="model"
        />
        <label
          :for="radio.value"
          class="ml-3 block text-sm font-medium leading-6 text-gray-900"
          >{{ radio.title }}</label
        >
      </div>
    </div>
  </fieldset>
  <template v-if="model === true">
    <slot></slot>
  </template>
</template>
