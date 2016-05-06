import os
import math
import shutil
import glob
import time
def readFile(filename,header,seperator=","):

    output=[]
    fopen=open(filename)
    
    if header:
        fopen.readline()
    while True:
        line=fopen.readline()
        if line and len(line)>1:    #len(line)>1 : to avoid empty last line
            output.append(line.strip().split(seperator))
        else:
            fopen.close()
            break
        #print line
    return output

def dist(pt1_Lat,pt1_Lon,pt2_Lat,pt2_Lon):
    R = 6372800; #meter
    phi1 = math.radians(pt1_Lat)
    phi2 = math.radians(pt2_Lat)
    del_phi=math.radians((pt2_Lat-pt1_Lat))
    del_lambda = math.radians((pt2_Lon-pt1_Lon))
    a = (math.sin(del_phi/2) ** 2) + math.cos(phi1) * math.cos(phi2) * (math.sin(del_lambda/2) **2)
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1-a))
    d = R * c
    return d 

def main():
    raw=readFile('uploads/up_3.txt',True)
    seg=readFile('uploads/seg.txt',True)
    counter=1
    f1=open("heat.txt",'w')
    f1.write('lat,long,color\n')
    while(len(seg)>0):
        first=[seg[0][0],seg[0][1]]           #Give starting Coordinate
        last=[seg[0][2],seg[0][3]]      #Give end Coordinate
        counter=int(seg[0][4])
        flag=False
        for i,row in enumerate(raw):
            
            if(float(raw[i][0])==float(first[0]) and float(raw[i][1])==float(first[1]) and flag==False):
               flag=True

            elif(float(raw[i][0])==float(last[0]) and float(raw[i][1])==float(last[1])):
                flag=False
                f1.write(str(raw[i][0])+','+str(raw[i][1])+','+str(counter)+'\n')
                break
                
            if flag:
                f1.write(str(raw[i][0])+','+str(raw[i][1])+','+str(counter)+'\n')
        seg.pop(0)
        #counter+=1
        raw=raw[i:]
        i=0
    f1.close()
    
if __name__=='__main__':
    main()
