import { configureStore } from "@reduxjs/toolkit";
import { apiSlice } from "./slices/apiSlice";
import authReducer from "./slices/authSlice";
import adminReducer from "./slices/adminSlice";
import saveCredentialsReducer from "./slices/saveCredentials";
const store = configureStore({
  reducer: {
    auth: authReducer,
    admin: adminReducer,
    saveCredentials: saveCredentialsReducer,
    [apiSlice.reducerPath]: apiSlice.reducer,
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware().concat(apiSlice.middleware),
  devTools: true,
});
export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
export default store;
