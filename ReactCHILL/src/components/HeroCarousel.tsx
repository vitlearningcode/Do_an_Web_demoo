import { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

const slides = [
  {
    id: 1,
    image: 'https://picsum.photos/seed/banner1/1200/400',
    badge: 'Khuyến mãi tháng 10',
    title: 'Hội Sách Mùa Thu \n Giảm Giá Lên Đến 50%',
    description: 'Khám phá hàng ngàn tựa sách hấp dẫn với mức giá ưu đãi nhất trong năm. Miễn phí giao hàng toàn quốc.',
    buttonText: 'Mua Ngay',
    color: 'from-blue-900 via-blue-900/80'
  },
  {
    id: 2,
    image: 'https://picsum.photos/seed/banner2/1200/400',
    badge: 'Sách Mới',
    title: 'Tuần Lễ Sách Mới \n Tặng Kèm Bookmark',
    description: 'Cập nhật những tựa sách mới nhất từ các nhà xuất bản hàng đầu. Quà tặng độc quyền cho 100 đơn hàng đầu tiên.',
    buttonText: 'Khám Phá',
    color: 'from-emerald-900 via-emerald-900/80'
  },
  {
    id: 3,
    image: 'https://picsum.photos/seed/banner3/1200/400',
    badge: 'Độc Quyền',
    title: 'Bộ Sách Kỹ Năng \n Dành Cho Sinh Viên',
    description: 'Trang bị hành trang vững chắc cho tương lai với bộ sách kỹ năng thiết yếu. Giảm thêm 10% cho học sinh, sinh viên.',
    buttonText: 'Xem Chi Tiết',
    color: 'from-purple-900 via-purple-900/80'
  }
];

export function HeroCarousel() {
  const [currentSlide, setCurrentSlide] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slides.length);
    }, 5000);
    return () => clearInterval(timer);
  }, []);

  const nextSlide = () => setCurrentSlide((prev) => (prev + 1) % slides.length);
  const prevSlide = () => setCurrentSlide((prev) => (prev - 1 + slides.length) % slides.length);

  return (
    <div className="relative rounded-3xl overflow-hidden h-[400px] md:h-[450px] group">
      {slides.map((slide, index) => (
        <div 
          key={slide.id}
          className={`absolute inset-0 transition-opacity duration-1000 ease-in-out ${
            index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'
          }`}
        >
          <div className="absolute inset-0 z-0">
            <img src={slide.image} alt="Banner" className="w-full h-full object-cover opacity-50" referrerPolicy="no-referrer" />
            <div className={`absolute inset-0 bg-gradient-to-r ${slide.color} to-transparent`}></div>
          </div>
          <div className="relative z-10 p-8 md:p-16 max-w-2xl h-full flex flex-col justify-center">
            <span className="inline-block px-4 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-full mb-6 uppercase tracking-wider w-max shadow-lg shadow-orange-500/30">
              {slide.badge}
            </span>
            <h2 className="text-4xl md:text-5xl font-bold leading-tight mb-6 text-white whitespace-pre-line">
              {slide.title}
            </h2>
            <p className="text-gray-200 text-lg mb-8 line-clamp-2 md:line-clamp-none">
              {slide.description}
            </p>
            <button className="px-8 py-3.5 bg-white text-gray-900 font-bold rounded-full hover:bg-gray-100 transition-colors shadow-xl w-max flex items-center gap-2 group/btn">
              {slide.buttonText}
              <ChevronRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
            </button>
          </div>
        </div>
      ))}

      {/* Navigation Arrows */}
      <button 
        onClick={prevSlide}
        className="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all z-20"
      >
        <ChevronLeft className="w-6 h-6" />
      </button>
      <button 
        onClick={nextSlide}
        className="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all z-20"
      >
        <ChevronRight className="w-6 h-6" />
      </button>

      {/* Indicators */}
      <div className="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-20">
        {slides.map((_, index) => (
          <button
            key={index}
            onClick={() => setCurrentSlide(index)}
            className={`h-2 rounded-full transition-all duration-300 ${
              index === currentSlide ? 'w-8 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'
            }`}
          />
        ))}
      </div>
    </div>
  );
}
