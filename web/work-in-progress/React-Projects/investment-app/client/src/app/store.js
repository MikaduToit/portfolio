import { configureStore } from "@reduxjs/toolkit";

import { authApi } from "./api/authApi";
import authReducer from "./authorization/authSlice";

export const store = configureStore({
  reducer: {
    [authApi.reducerPath]: authApi.reducer,
    auth: authReducer,
  },
  //Needed for RTK Query.
  middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(authApi.middleware),
  devTools: true,
});
