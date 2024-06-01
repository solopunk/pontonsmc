<script setup>
import axios from "axios";
import { reactive } from "vue";

import InputComp from "@/components/InputComp.vue";
import BtnComp from "@/components/BtnComp.vue";

const request = reactive({
  type: "supporter",
  member: {
    email: "",
    first: "",
    last: "",
    birthdate: "",
    address: "",
    postal_code: "",
    city: "",
    phone: "",
    job: "",
  },
});

const inputs = [
  {
    label: "Courriel",
    type: "email",
    required: true,
    model: request.member.email,
  },
  {
    label: "Prénom",
    type: "text",
    required: true,
    model: request.member.first,
  },
  {
    label: "Nom",
    type: "text",
    required: true,
    model: request.member.last,
  },
];

const errors = reactive({});

async function addMember() {
  try {
    const response = await axios.post("/api/member", request);
    // Réinitialiser les erreurs en cas de succès
    for (let key in errors) {
      errors[key] = null;
    }
  } catch (error) {
    if (error.response && error.response.status === 422) {
      // Réinitialiser les erreurs existantes
      for (let key in errors) {
        errors[key] = null;
      }
      // Affecter les nouvelles erreurs de validation
      Object.assign(errors, error.response.data.errors);
    } else {
      console.error("failed", error.response?.data || error.message);
    }
  }
}
</script>

<template>
  <form @submit.prevent="addMember">
    <div class="grid grid-cols-1 gap-3 space-y-4 sm:grid-cols-2">
      <!-- <InputComp
        label="Courriel"
        i-type="email"
        required
        v-model="request.member.email"
      />
      <InputComp
        label="Prénom"
        i-type="text"
        required
        v-model="request.member.first"
      />
      <InputComp
        label="Nom de famille"
        i-type="text"
        required
        v-model="request.member.last"
      /> -->

      

      <InputComp
        label="Date de naissance"
        i-type="date"
        required
        v-model="request.member.birthdate"
      />
      <InputComp
        label="Adresse"
        i-type="text"
        required
        v-model="request.member.address"
      />
      <InputComp
        label="Code postal"
        i-type="text"
        required
        v-model="request.member.postal_code"
      />
      <InputComp
        label="Ville"
        i-type="text"
        required
        v-model="request.member.city"
      />
      <InputComp
        label="Numéro de téléphone"
        i-type="tel"
        required
        v-model="request.member.phone"
      />
      <InputComp
        label="Travail"
        i-type="text"
        required
        v-model="request.member.job"
      />
      <BtnComp submit class="col-span-2">Créer</BtnComp>
    </div>
  </form>
</template>
