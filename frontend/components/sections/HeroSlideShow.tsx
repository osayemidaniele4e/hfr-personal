"use client";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/pagination";
import "../../app/swiperstyles.css";

import { Pagination, Autoplay, Navigation } from "swiper/modules";
import { FaArrowLeft, FaArrowRight } from "react-icons/fa";
import MainPageBanner from "./MainPageBanner";
import { useEffect, useState } from "react";

import axios from "axios";

interface SliderItem {
  id: number;
  image_url: string;
  title: string;
  sub_title: string;
  // You can add more fields based on your backend response
}

const HeroSlideShow = () => {
  const [swiperData, setSwiperData] = useState<SliderItem[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    // Fetch the data from the backend API
    const fetchSliderData = async () => {
      try {
        const response = await axios.get(
          `${process.env.NEXT_PUBLIC_BACKEND_API}/slider`
        );

        // console.error("API response is not an array:", response.data.data);
        setSwiperData(response.data.data); // Assuming the backend returns an array of slider items
        setLoading(false);
      } catch (error) {
        setError("Failed to fetch slideshow data");
        setLoading(false);
      }
    };

    fetchSliderData();
  }, []);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>{error}</div>;

  return (
    <div className="relative ">
      <Swiper
        autoplay={{
          delay: 4000,
          disableOnInteraction: true,
        }}
        className="swiper"
        navigation={{ nextEl: ".next", prevEl: ".prev" }}
        spaceBetween={30}
        pagination={{
          clickable: true,
        }}
        modules={[Autoplay, Pagination, Navigation]}
      >
        {swiperData?.map((item, index) => (
          <SwiperSlide key={index}>
            <MainPageBanner items={item} index={index} />
          </SwiperSlide>
        ))}
      </Swiper>
    </div>
  );
};
export default HeroSlideShow;
