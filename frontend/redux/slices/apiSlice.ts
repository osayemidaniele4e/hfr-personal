import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { getCookie } from "cookies-next";
const baseQuery = fetchBaseQuery({
  baseUrl: process.env.NEXT_PUBLIC_BACKEND_API,
  prepareHeaders: (headers) => {
    const token = getCookie("USER");
    headers.set("Authorization", `Bearer ${token}`);
    // headers.set("accept", "application/json");
    // if(!headers.has("Content-Type")){
    //   headers.set("Content-Type","");
    // }
   
  },
});

export const apiSlice = createApi({
  baseQuery,
  endpoints: (builder) => ({}),
});
