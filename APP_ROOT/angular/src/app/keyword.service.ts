import { Injectable } from '@angular/core';
import { Http, Headers, RequestOptions } from '@angular/http';
import 'rxjs/add/operator/map';

@Injectable()
export class KeywordService {
  result:any;

  constructor(private _http: Http) { }

  getKeywords(){
    return this._http.get("http://localhost:8080/")
      .map(result => this.result = result.json());
  }

}
