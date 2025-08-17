/* eslint-disable prettier/prettier */


import { useState } from "react";
import { Image } from "@/types";

export default function Carousel({ images }: { images: Image[] }) {
  const [activeIndex, setActiveIndex] = useState(0);
  
  return (
    <div className="flex items-start gap-6">
      {/* Thumbnails */}
      <div className="flex flex-col gap-3">
        {images.map((image, index) => (
          <button
            key={image.id}
            onClick={() => setActiveIndex(index)}
            className={`border rounded-lg overflow-hidden w-16 h-16 flex items-center justify-center
              hover:border-blue-500 transition
              ${activeIndex === index ? "border-blue-500 ring-2 ring-blue-300" : "border-gray-300"}`}
          >
            <img
              src={image.thumb}
              alt={`Thumbnail ${index + 1}`}
              className="object-cover w-full h-full"
            />
          </button>
        ))}
      </div>

      {/* Main Image */}
      <div className="flex-1">
        <img
          src={images[activeIndex].large}
          alt={`Image ${activeIndex + 1}`}
          className="rounded-xl shadow-lg w-full max-h-[500px] object-contain"
        />
      </div>
    </div>
  );
}
