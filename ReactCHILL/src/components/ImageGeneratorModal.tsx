import { useState } from 'react';
import { X, Image as ImageIcon, Loader2, Download, Check } from 'lucide-react';
import { GoogleGenAI } from '@google/genai';

interface ImageGeneratorModalProps {
  isOpen: boolean;
  onClose: () => void;
  bookName: string;
  onImageSelect: (imageUrl: string) => void;
}

export function ImageGeneratorModal({ isOpen, onClose, bookName, onImageSelect }: ImageGeneratorModalProps) {
  const [prompt, setPrompt] = useState(`Thiết kế bìa sách cho cuốn sách có tựa đề "${bookName}", phong cách hiện đại, chuyên nghiệp, độ phân giải cao.`);
  const [size, setSize] = useState<'1K' | '2K' | '4K'>('1K');
  const [isGenerating, setIsGenerating] = useState(false);
  const [generatedImage, setGeneratedImage] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  if (!isOpen) return null;

  const handleGenerate = async () => {
    if (!prompt.trim() || isGenerating) return;
    
    setIsGenerating(true);
    setError(null);
    setGeneratedImage(null);

    try {
      const ai = new GoogleGenAI({ apiKey: process.env.GEMINI_API_KEY });
      const response = await ai.models.generateContent({
        model: 'gemini-3.1-flash-image-preview',
        contents: {
          parts: [
            { text: prompt }
          ]
        },
        config: {
          // @ts-ignore - The types might not be fully up to date in the SDK version
          imageConfig: {
            aspectRatio: "3:4",
            imageSize: size
          }
        }
      });

      let foundImage = false;
      for (const part of response.candidates?.[0]?.content?.parts || []) {
        if (part.inlineData) {
          const base64EncodeString = part.inlineData.data;
          const imageUrl = `data:${part.inlineData.mimeType || 'image/png'};base64,${base64EncodeString}`;
          setGeneratedImage(imageUrl);
          foundImage = true;
          break;
        }
      }

      if (!foundImage) {
        setError('Không thể tạo ảnh. Vui lòng thử lại với prompt khác.');
      }
    } catch (err) {
      console.error('Error generating image:', err);
      setError('Đã có lỗi xảy ra khi tạo ảnh. Vui lòng thử lại sau.');
    } finally {
      setIsGenerating(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
        <div className="flex justify-between items-center p-6 border-b border-gray-100">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-50 text-blue-600 rounded-lg">
              <ImageIcon className="w-5 h-5" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-gray-800">Tạo ảnh bìa AI</h2>
              <p className="text-sm text-gray-500">Sử dụng Gemini 3.1 Flash Image Preview</p>
            </div>
          </div>
          <button onClick={onClose} className="p-2 text-gray-400 hover:bg-gray-100 rounded-xl transition-colors">
            <X className="w-5 h-5" />
          </button>
        </div>

        <div className="flex flex-col md:flex-row flex-1 overflow-hidden">
          {/* Controls */}
          <div className="w-full md:w-1/2 p-6 border-r border-gray-100 flex flex-col gap-6 overflow-y-auto">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Prompt (Mô tả ảnh)</label>
              <textarea
                value={prompt}
                onChange={(e) => setPrompt(e.target.value)}
                rows={4}
                className="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all resize-none"
                placeholder="Mô tả chi tiết bìa sách bạn muốn tạo..."
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Kích thước ảnh</label>
              <div className="grid grid-cols-3 gap-3">
                {(['1K', '2K', '4K'] as const).map((s) => (
                  <button
                    key={s}
                    onClick={() => setSize(s)}
                    className={`py-2 px-3 rounded-xl border text-sm font-medium transition-all ${
                      size === s 
                        ? 'border-blue-600 bg-blue-50 text-blue-600' 
                        : 'border-gray-200 text-gray-600 hover:bg-gray-50'
                    }`}
                  >
                    {s}
                  </button>
                ))}
              </div>
            </div>

            <button
              onClick={handleGenerate}
              disabled={isGenerating || !prompt.trim()}
              className="mt-auto w-full py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              {isGenerating ? (
                <>
                  <Loader2 className="w-5 h-5 animate-spin" />
                  Đang tạo ảnh...
                </>
              ) : (
                <>
                  <ImageIcon className="w-5 h-5" />
                  Tạo ảnh bìa
                </>
              )}
            </button>
          </div>

          {/* Preview */}
          <div className="w-full md:w-1/2 bg-gray-50 p-6 flex flex-col items-center justify-center min-h-[300px]">
            {isGenerating ? (
              <div className="flex flex-col items-center text-gray-400 gap-4">
                <Loader2 className="w-10 h-10 animate-spin text-blue-600" />
                <p className="text-sm font-medium">AI đang vẽ bìa sách...</p>
              </div>
            ) : generatedImage ? (
              <div className="flex flex-col items-center w-full h-full">
                <div className="flex-1 w-full flex items-center justify-center overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white p-2">
                  <img 
                    src={generatedImage} 
                    alt="Generated Book Cover" 
                    className="max-w-full max-h-full object-contain rounded-lg"
                  />
                </div>
                <div className="flex gap-3 mt-6 w-full">
                  <button 
                    onClick={() => {
                      onImageSelect(generatedImage);
                      onClose();
                    }}
                    className="flex-1 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors flex items-center justify-center gap-2"
                  >
                    <Check className="w-4 h-4" />
                    Sử dụng ảnh này
                  </button>
                  <a 
                    href={generatedImage} 
                    download={`cover-${bookName.replace(/\s+/g, '-').toLowerCase()}.png`}
                    className="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors flex items-center justify-center"
                    title="Tải xuống"
                  >
                    <Download className="w-4 h-4" />
                  </a>
                </div>
              </div>
            ) : error ? (
              <div className="text-center p-6 bg-red-50 rounded-xl border border-red-100">
                <p className="text-red-600 text-sm font-medium">{error}</p>
              </div>
            ) : (
              <div className="flex flex-col items-center text-gray-400 gap-3">
                <ImageIcon className="w-12 h-12 opacity-50" />
                <p className="text-sm font-medium">Ảnh tạo ra sẽ hiển thị ở đây</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
