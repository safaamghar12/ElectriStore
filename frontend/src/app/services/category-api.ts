import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class CategoryApi {

    private apiUrl ='http://localhost:8000/api/categories';

    constructor(private http: HttpClient){

    }

    getCategories(){
        this.http.get(this.apiUrl);
    }


}
