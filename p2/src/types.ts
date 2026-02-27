export type ViewType = 
  | 'import' 
  | 'book-info' 
  | 'revenue' 
  | 'sales' 
  | 'inventory' 
  | 'reports';

export interface Book {
  id: string;
  name: string;
  author: string;
  category: string;
  price: number;
  originalPrice?: number;
  rating: number;
  reviews: number;
  image: string;
  badge?: string;
  description?: string;
  publisher?: string;
  pages?: number;
}
