# GetInfo
## User Manual

The Get Info is a PHP class that opens the xml files and analizes the data.
Returning a JSON file with the information sorted.

### Request:

* URL: GetInfo.php
* METHOD: get
### The JSON File:

It is formed by the following fields:

  - section
     - [sectionId] -> section information
  - sub-section
     - [sectionId][subSectionId] - sub section information
  - element
     - [sectionId][subSectionId][elementId] - Element Information

### Notes:
The XML files have been gathered from the data_cms folder. But there is an identical folder called data. Need to figure why the CMS creates two of them.
