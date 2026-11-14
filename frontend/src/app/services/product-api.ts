import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CreateProductRequest, Product } from '../models';


@Injectable({
  providedIn: 'root'
})
export class ProductApi {
    
  private apiUrl = 'http://localhost:8000/api/products';

  constructor(private http: HttpClient) {}

  getProducts() {
    return this.http.get(this.apiUrl);
  }

  createProduct(product: CreateProductRequest) {
    return this.http.post<Product>(this.apiUrl, product);
  }
}
