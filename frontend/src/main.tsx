import { StrictMode, Suspense, lazy } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Loading from "./components/Loading";

const Home = lazy(() => import('./pages/Home'));


createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <BrowserRouter>
      <Suspense fallback={<Loading />}>
        <Routes>
          <Route path="/" element={<Home />} />
          
          {/* สามารถเพิ่ม Route อื่น ๆ ได้ที่นี่ */}
          
          <Route path="*" element={<h1>404 Not Found</h1>} />
        </Routes>
      </Suspense>
    </BrowserRouter>
  </StrictMode>
)
