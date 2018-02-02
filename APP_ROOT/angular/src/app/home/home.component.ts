import { Component, OnInit } from '@angular/core';
import { KeywordService} from '../keyword.service';
import { Keyword } from '../keyword';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  keywords: Array<Keyword>;

  constructor(private _keywordsService: KeywordService) { }

  ngOnInit() {
    this._keywordsService.getKeywords()
      .subscribe(res => this.keywords = res);
  }

}
