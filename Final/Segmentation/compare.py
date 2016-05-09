import os
import math
import sys
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
    x=sys.argv
    gt_name="GroundTruth/"+x[1]    #raw_input("Enter ground truth file : ")
    tr_name="SegmentedTrails/"+x[2]    #raw_input("Enter segmented trail file : ")
    gt=readFile(gt_name,True)
    tr=readFile(tr_name,True)
    #print gt[0],tr[0],tr[len(tr)-1]
    flist=[]
    elist=[]
    for gdata in gt:
        d=dist(float(gdata[0]),float(gdata[1]),float(tr[0][0]),float(tr[0][1]))
        d1=dist(float(gdata[0]),float(gdata[1]),float(tr[len(tr)-1][0]),float(tr[len(tr)-1][1]))
        flist.append(d)
        elist.append(d1)
    a=flist.index(min(flist))
    b=elist.index(min(elist))
    #print a,b
    #print flist,elist
    #print gt[a:b]
    if os.path.exists("Seg_gt.txt"):
        os.remove("Seg_gt.txt")
    f2=open("Seg_gt.txt","w")
    f2.write('lat,long,time,id\n')
    for each in gt[a:b+1]:
        f2.write(each[0]+','+each[1]+','+each[2]+','+each[-1]+'\n')
    f2.close()
if __name__ == '__main__':
        main() 
